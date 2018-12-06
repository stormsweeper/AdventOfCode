<?php
$minX = $minY = PHP_INT_MAX;
$maxX = $maxY = 0;
$coords = array_filter(array_map('trim', file($argv[1])));
$coords = array_combine(
    $coords,
    array_map(
        function($line) use (&$minX, &$minY, &$maxX, &$maxY) {
            list ($x, $y) = array_map('intval', explode(',', $line));
            $minX = min($minX, $x);
            $minY = min($minY, $y);
            $maxX = max($maxX, $x);
            $maxY = max($maxY, $y);
            return [$x, $y];
        },
        $coords
    )
);

function manhattanDistance($coordsA, $coordsB) {
    return abs($coordsA[0] - $coordsB[0]) + abs($coordsA[1] - $coordsB[1]);
}

function prox($coords, $refCoord, $sorter) {
    if (count($coords) === 1) {
        return (array_keys($coords))[0];
    }

    $dists = array_map(
        function($coord) use ($refCoord) {
            return manhattanDistance($coord, $refCoord);
        },
        $coords
    );
    if (is_callable($sorter)) {
        $sorter($dists);
    }
    list ($c1, $c2) = array_keys($dists);
    if ($dists[$c1] === $dists[$c2]) {
        return null;
    }
    return $c1;
}

function nearest($coords, $refCoord) { return prox($coords, $refCoord, 'asort'); }
function furthest($coords, $refCoord) { return prox($coords, $refCoord, 'arsort'); }

$infs = [];
foreach (range($minX, $maxX) as $x) {
    foreach (range($minY, $maxY) as $y) {
        $left = [$minX, $y];
        $right = [$maxX, $y];
        $top = [$x, $minY];
        $bottom = [$x, $maxY];
        foreach ([$left, $right, $top, $bottom] as $pos) {
            $n = nearest($coords, $pos);
            if (isset($n) && !isset($infs[$n])) {
                $infs[$n] = 1;
            }
        }
    }
}

$candidates = array_diff_key($coords, $infs);

$grid = [];

foreach (range($minX, $maxX) as $x) {
    foreach (range($minY, $maxY) as $y) {
        $n = nearest($coords, [$x, $y]);
        if (isset($n) && isset($candidates[$n])) {
            $grid[] = $n;
        }
    }
}

$sizes = array_count_values($grid);

echo max($sizes);
