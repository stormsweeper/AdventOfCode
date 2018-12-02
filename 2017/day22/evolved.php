<?php

// 0 up, 1 right, 2 down, 3
define('UP', 0);
define('RIGHT', 1);
define('DOWN', 2);
define('LEFT', 3);

define('TURN_LEFT', -1);
define('TURN_RIGHT', 1);
define('TURN_180', 2);
define('TURN_NONE', 0);

define('STATE_CLEAN', 0);
define('STATE_WEAK', 1);
define('STATE_INFECTED', 2);
define('STATE_FLAGGED', 3);

function readState(int $x, int $y) {
    global $map;
    return $map[$y][$x] ?? STATE_CLEAN;
}

function updateState(int $x, int $y) {
    switch (readState($x, $y)) {
        case STATE_CLEAN:    setState($x, $y, STATE_WEAK); break;
        case STATE_WEAK:     setState($x, $y, STATE_INFECTED); break;
        case STATE_INFECTED: setState($x, $y, STATE_FLAGGED); break;
        case STATE_FLAGGED:  setState($x, $y, STATE_CLEAN); break;
    }
}

function setState(int $x, int $y, int $state) {
    global $map, $total;

    if ($state === STATE_CLEAN) {
        unset($map[$y][$x]);
        return;
    }

    if ($state === STATE_INFECTED) {
        $total++;
    }

    if (!isset($map[$y])) {
        $map[$y] = [];
    }
    $map[$y][$x] = $state;
}

function turn($x, $y, $facing): int /* a facing constant */ {
    switch (readState($x, $y)) {
        case STATE_CLEAN:    $adj = TURN_LEFT;  break;
        case STATE_WEAK:     $adj = TURN_NONE;  break;
        case STATE_INFECTED: $adj = TURN_RIGHT; break;
        case STATE_FLAGGED:  $adj = TURN_180;   break;
    }

    // e.g. Up, turn left:
    //     (0 + -1 + 4 = 3)
    return ($facing + $adj + 4) % 4;
}

function move($x, $y, $facing): array {
    switch ($facing) {
        case UP:    $y--; break;
        case DOWN:  $y++; break;
        case LEFT:  $x--; break;
        case RIGHT: $x++; break;
    }
    return [$x, $y];
}

$facing = UP;
$map = [];
$virusX = $virusY = 0;
$total = 0;

$input = explode("\n", file_get_contents($argv[1]));
$bursts = intval($argv[2] ?? 1);

// build the map
$offsetY = floor(count($input) / 2);
$offsetX = floor(strlen($input[0]) / 2);
foreach ($input as $y => $line) {
    $offset = 0;
    while ($offset < strlen($line) && ($x = strpos($line, '#', $offset)) !== false) {
        $offset = $x + 1;
        setState($x - $offsetX, $y - $offsetY, STATE_INFECTED);
    }
}


// reset the infected count
$total = 0;

while ($bursts--) {
    $facing = turn($virusX, $virusY, $facing);
    updateState($virusX, $virusY);
    list ($virusX, $virusY) = move($virusX, $virusY, $facing);
}

echo $total;

