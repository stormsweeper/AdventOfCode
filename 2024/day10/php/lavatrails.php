<?php

$map = explode("\n", trim(file_get_contents($argv[1])));
$map_height = count($map);
$map_width = strlen($map[0]);

$trailheads = [];
$trailends = [];

$scores = [];

function elev(array $map, int $x, int $y): int {
    $map_height = count($map);
    $map_width = strlen($map[0]);
    if ($x < 0 || $x >= $map_width || $y < 0 || $y >= $map_height) return -100;
    if ($map[$y][$x] === '.') return PHP_INT_MAX;
    return intval($map[$y][$x]);
}

function flow_down(array $map, array &$scores, int $x, int $y): void {
    $cur_el = elev($map, $x, $y);
    if ($cur_el < 1 || $cur_el > 9) return;
    foreach ([-1, 0, 1] as $dx) {
        foreach ([-1, 0, 1] as $dy) {
            $nx = $x + $dx; $ny = $y + $dy;
            if (elev($map, $nx, $ny) !== $cur_el - 1) continue;
            inc_score($scores, $nx, $ny);
            flow_down($map, $scores, $nx, $ny);
        }
    }
}

function inc_score(array &$scores, int $x, int $y): void {
    if (!isset($scores[$y])) $scores[$y] = [];
    $scores[$y][$x] = ($scores[$y][$x] ?? 0) + 1;
}

for ($y = 0; $y < $map_height; $y++) {
    for ($x = 0; $x < $map_width; $x++) {
        $el = elev($map, $x, $y);
        if ($el === 0) $trailheads[] = [$x, $y];
        if ($el === 9) $trailends[] = [$x, $y];
    }
}

foreach ($trailends as [$te_x, $te_y]) {
    flow_down($map, $scores, $te_x, $te_y);
}

$p1 = 0;

foreach ($trailheads as $i => [$th_x, $th_y]) {
    echo "{$i}: {$scores[$th_y][$th_x]}\n";
    $p1 += ($scores[$th_y][$th_x] ?? 0);
}

echo "p1: {$p1}\n";

