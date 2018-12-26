<?php

$input = file($argv[1]);

[,$depth] = explode(': ', $input[0]);
$depth = intval($depth);

[,$coords] = explode(': ', $input[1]);
$coords = array_map('intval', explode(',', $coords));

$geo_indices = [];
$erosion_levels = [];

function geologicIndex($x, $y) {
    global $depth, $coords, $geo_indices;

    //The region at 0,0 (the mouth of the cave) has a geologic index of 0.
    if ($x === 0 && $y === 0) {
        return 0;
    }

    //The region at the coordinates of the target has a geologic index of 0.
    if ([$x,$y] === $coords) {
        return 0;
    }

    //If the region's Y coordinate is 0, the geologic index is its X coordinate times 16807.
    if ($y === 0) {
        return $x * 16807;
    }

    //If the region's X coordinate is 0, the geologic index is its Y coordinate times 48271.
    if ($x === 0) {
        return $y * 48271;
    }

    //Otherwise, the region's geologic index is the result of multiplying the erosion levels of the regions at X-1,Y and X,Y-1.
    $key = "{$x},{$y}";
    if (!isset($geo_indices[$key])) {
        $geo_indices[$key] = erosionLevel($x - 1, $y) * erosionLevel($x, $y - 1);
    }
    return $geo_indices[$key];
}

function erosionLevel($x, $y) {
    global $depth, $coords, $erosion_levels;
    $key = "{$x},{$y}";
    if (!isset($erosion_levels[$key])) {
        $erosion_levels[$key] = (geologicIndex($x, $y) + $depth) % 20183;
    }
    return $erosion_levels[$key];
}

function dangerLevel($x, $y) {
    return erosionLevel($x, $y) % 3;
}

$danger = 0;
for ($y = 0; $y <= $coords[1]; $y++) {
    for ($x = 0; $x <= $coords[0]; $x++) {
        $danger += dangerLevel($x, $y);
    }
}

echo $danger;