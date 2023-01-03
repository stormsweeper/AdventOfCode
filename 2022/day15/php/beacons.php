<?php

#  ¯\_(ツ)_/¯
ini_set('memory_limit', '2G');

$data = trim(file_get_contents($argv[1]));
$scan_y = intval($argv[2]);
$max_coord = intval($argv[3]);

preg_match_all('/Sensor at x=(-?\d+), y=(-?\d+): closest beacon is at x=(-?\d+), y=(-?\d+)/', $data, $matches, PREG_SET_ORDER);


function manhattanDistance(int $x1, int $y1, int $x2, int $y2): int {
    return abs($x1 - $x2) + abs($y1 - $y2);
}

function pos2key(int $x, int $y): string {
    return "{$x},{$y}";
}

$impossible = [];
$sensors = [];

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
    $sensors[] = [$sensor_x, $sensor_y, $sensor_range];

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

$p1 = array_sum($impossible);

echo "p1: {$p1}\n";

// sort the sensors from smallest range to longest
usort(
    $sensors,
    function(array $a, array $b) { return $a[2] <=> $b[2]; }
);

$checked = [];

$found_x = $found_y = -1;

foreach ($sensors as [$start_x, $start_y, $sweep_dist]) {
    // get range, add 1
    $sweep_dist++;
    // sweep around at that dist, start at top-most point
    $start_y += $sweep_dist;
    for ($delta = 0; $delta <= $sweep_dist; $delta++) {
        $sweeps = [
            [($start_x + $delta), ($start_y - $delta)], // top right
            [($start_x + $delta) * -1, ($start_y - $delta)], // top left
            [($start_x + $delta), ($start_y - $delta) * -1], // bottom right
            [($start_x + $delta) * -1, ($start_y - $delta) * -1], // bottom left
        ];
        foreach ($sweeps as [$sweep_x, $sweep_y]) {
            // if oob, continue
            if ($sweep_x < 0 || $sweep_x > $max_coord || $sweep_y < 0 || $sweep_y > $max_coord) continue;
            // if checked, skip
            $sweep_key = pos2key($sweep_x, $sweep_y);
            if (isset($checked[$sweep_key])) continue;
            // if out of range of all sensors, found spot
            foreach ($sensors as [$sensor_x, $sensor_y, $sensor_range]) {
                $dist = manhattanDistance($sensor_x, $sensor_y, $sweep_x, $sweep_y);
                if ($dist <= $sensor_range) {
                    $checked[$sweep_key] = true;
                    continue 2;
                }
            }
            $found_x = $sweep_x;
            $found_y = $sweep_y;
            break 3;
        }
    }

}

$p2 = $found_x * 4000000 + $found_y;

echo "p2: {$p2}\n";
