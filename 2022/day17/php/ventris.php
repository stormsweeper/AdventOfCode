<?php

require_once 'rocks.php';

$vent_pattern = trim(file_get_contents($argv[1]));
$max_blocks = intval($argv[2]);
$width = 7;

$chamber = [];
$max_height = 0;
$num_rocks = 0;
$current_rock = null;

function pos2key(int $x, int $y): string {
    return "{$x},{$y}";
}

function key2pos(string $key): array {
    [$x, $y] = explode(',', $key);
    return [intval($x), intval($y)];
}

function next_rock(): Rock {
    global $max_height, $num_rocks;
    switch($num_rocks++ % 5) {
        case 0: return new HorzLine($max_height);
        case 1: return new Cross($max_height);
        case 2: return new Ell($max_height);
        case 3: return new VertLine($max_height);
        case 4: return new Square();
    }
}


