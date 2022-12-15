<?php

#  ¯\_(ツ)_/¯
ini_set('memory_limit', '2G');

$data = trim(file_get_contents($argv[1]));
$scan_y = intval($argv[2]);

preg_match_all('/Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)/', $data, $matches, PREG_SET_ORDER);


function manhattanDistance(int $x1, int $y1, int $x2, int $y2): int {
    return abs($x1 - $x2) + abs($y1 - $y2);
}

function pos2key(int $x, int $y): string {
    return "{$x},{$y}";
}

$impossible = [];

foreach ($matches as $m) {
    $sensor_x = intval($m[1]);
    $sensor_y = intval($m[2]);
    $beacon_x = intval($m[3]);
    $beacon_y = intval($m[4]);
    // can't put a beacon where a beacon or sensor already exists 
    if ($beacon_y === $scan_y) $impossible[$beacon_x] = false;
    if ($sensor_y === $scan_y) $impossible[$sensor_x] = false;
    // on
    $sensor_range = manhattanDistance($sensor_x, $sensor_y, $beacon_x, $beacon_y);
    $ydist = abs($sensor_y - $scan_y);
    // out of scan range
    if ($ydist > $sensor_range) continue;
    $xdist = $sensor_range - $ydist;
    $min_x = $sensor_x - $xdist;
    $max_x = $sensor_x + $xdist;
    for ($scan_x = $min_x; $scan_x <= $max_x; $scan_x++) {
        if (!isset($impossible[$scan_x])) $impossible[$scan_x] = true;
    }
}

echo array_sum($impossible);