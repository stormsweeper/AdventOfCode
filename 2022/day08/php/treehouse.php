<?php

$trees = trim(file_get_contents($argv[1]));
$width = strpos($trees, "\n");
$trees = str_replace("\n", '', $trees);
$trees = array_map('intval', str_split($trees));
$grid_size = count($trees);
$height = $grid_size / $width;
$max_x = $width - 1;
$max_y = $height - 1;

$visible = [];

function pos2i(int $x, int $y): string {
    global $width;
    return ($y * $width) + $x;
}
function i2pos(int $i): array {
    global $width;
    return [$i%$width, floor($i/$width)];
}

$edge_trees = 2 * ($width + $height - 2);

// up/down
for ($x = 1; $x < $max_x; $x++) {
    // cast shadows down
    $y = 0;
    $shade = $trees[pos2i($x, $y)];
    while (($shade < 9) && ($y++ < $max_y - 1)) {
        $down = pos2i($x, $y);
        if ($trees[$down] > $shade) {
            $visible[$down] = true;
            $shade = $trees[$down];
        }
    }
    // cast shadows up
    $y = $max_y;
    $shade = $trees[pos2i($x, $y)];
    while (($shade < 9) && ($y-- > 1)) {
        $up = pos2i($x, $y);
        if ($trees[$up] > $shade) {
            $visible[$up] = true;
            $shade = $trees[$up];
        }
    }
}

// right/left
for ($y = 1; $y < $max_y; $y++) {
    // cast shadows right
    $x = 0;
    $shade = $trees[pos2i($x, $y)];
    while (($shade < 9) && ($x++ < $max_x - 1)) {
        $right = pos2i($x, $y);
        if ($trees[$right] > $shade) {
            $visible[$right] = true;
            $shade = $trees[$right];
        }
    }
    // cast shadows left
    $x = $max_x;
    $shade = $trees[pos2i($x, $y)];
    while (($shade < 9) && ($x-- > 1)) {
        $left = pos2i($x, $y);
        if ($trees[$left] > $shade) {
            $visible[$left] = true;
            $shade = $trees[$left];
        }
    }
}

$p1 = count($visible) + $edge_trees;

echo $p1;
