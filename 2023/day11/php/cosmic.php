<?php

$map = trim(file_get_contents($argv[1]));
$map = explode("\n", $map);
$width = strlen($map[0]);
$height = count($map);

$expansion = intval($argv[2] ?? 2);

$galaxies = $non_empty_rows = $non_empty_cols = [];

foreach ($map as $y => $line) {
    for ($x = 0; $x < $width; $x++) {
        if ($map[$y][$x] === '#') {
            $galaxies[] = [$x,$y];
            $non_empty_cols[$x] = $x;
            $non_empty_rows[$y] = $y;
        }
    }
}

$empty_rows = array_diff(range(0, $width - 1), $non_empty_rows);
$empty_cols = array_diff(range(0, $height - 1), $non_empty_cols);

$galaxies = array_map(
    function($g) use ($empty_rows, $empty_cols, $expansion) {
        [$x, $y] = $g;
        $dx = $dy = 0;
        foreach ($empty_rows as $r) {
            if ($r < $y) $dy += $expansion - 1;
        }
        foreach ($empty_cols as $c) {
            if ($c < $x) $dx += $expansion - 1;
        }
        return [$x + $dx, $y + $dy];
    },
    $galaxies
);

function manhattanDistance(int $x1, int $y1, int $x2, int $y2): int {
    return abs($x1 - $x2) + abs($y1 - $y2);
}

$dists = 0;

foreach ($galaxies as $g1 => [$x1, $y1]) {
    foreach ($galaxies as $g2 => [$x2, $y2]) {
        if ($g1 >= $g2) continue;
        $dists += manhattanDistance($x1, $y1, $x2, $y2);
    }
}

echo $dists;
