<?php

// 0 up, 1 right, 2 down, 3
define('UP', 0);
define('RIGHT', 1);
define('DOWN', 2);
define('LEFT', 3);

define('TURN_LEFT', -1);
define('TURN_RIGHT', 1);

function isInfected(int $x, int $y) {
    global $map;
    return !empty($map[$y][$x]);
}

function infectOrClean(int $x, int $y) {
    global $map, $total;

    if (isInfected($x, $y)) {
        unset($map[$y][$x]);
    } else {
        $total++;
        if (!isset($map[$y])) {
            $map[$y] = [];
        }
        $map[$y][$x] = 1;
    }
}

function turn($x, $y, $facing): int /* a facing constant */ {
    $adj = isInfected($x, $y) ? TURN_RIGHT : TURN_LEFT;
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
        infectOrClean($x - $offsetX, $y - $offsetY);
    }
}


$pretty = array_map(
    function($line) use ($offsetX) {
        $str = str_repeat('.', $offsetX * 2 + 1);
        foreach ($line as $x => $val) {
            $i = $x + $offsetX;
            $str[$i] = '#';
        }
        return $str;
    },
    $map
);
echo implode("\n", $pretty);

// reset the infected count
$total = 0;

while ($bursts--) {
    $facing = turn($virusX, $virusY, $facing);
    infectOrClean($virusX, $virusY);
    list ($virusX, $virusY) = move($virusX, $virusY, $facing);
}

echo $total;

