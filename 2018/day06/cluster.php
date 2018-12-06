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

$maxdist = intval($argv[2]);

function manhattanDistance($coordsA, $coordsB) {
    return abs($coordsA[0] - $coordsB[0]) + abs($coordsA[1] - $coordsB[1]);
}

$size = 0;

foreach (range($minX, $maxX) as $x) {
    foreach (range($minY, $maxY) as $y) {
        $dists = array_map(
            function ($c) use ($x, $y) {
                return manhattanDistance($c, [$x, $y]);
            },
            $coords
        );
        if (array_sum($dists) < $maxdist) {
            //echo "{$x},{$y}\n";
            $size++;
        }
    }
}

echo $size;
