<?php

$serial_id = intval($argv[1] ?? 8);

function powerLevel($x, $y) {
    global $serial_id;
    $rack_id = $x + 10;
    $l = ($rack_id * $y + $serial_id) * $rack_id;
    return floor(($l % 1000) / 100) - 5;
}


$size = 300;
$summed = [];
for ($y = 1; $y <= $size; $y++) {
    if (!isset($summed[$y])) {
        $summed[$y] = [];
    }
    for ($x = 1; $x <= $size; $x++) {
        $sum = powerLevel($x, $y);
        $sum += $summed[$y][$x - 1] ?? 0;
        $sum += $summed[$y - 1][$x] ?? 0;
        $sum -= $summed[$y - 1][$x - 1] ?? 0;
        $summed[$y][$x] = $sum;
    }
}

function calculateArea($x, $y, $size) {
    global $summed;
    $mx = $x + $size - 1;
    $my = $y + $size - 1;
    $area = $summed[$my][$mx];
    if ($x > 1) {
        $area -= $summed[$my][$x - 1];
    }
    if ($y > 1) {
        $area -= $summed[$y - 1][$mx];
    }
    if ($x > 1 && $y > 1) {
        $area += $summed[$y - 1][$x - 1];
    }
    return $area;
}

$current_max = 0;
$current_key = null;

for ($s = 300; $s > 0; $s--) {
    $max_possible = $s * $s * 4;
    if ($max_possible < $current_max) {
        break;
    }
    $stop = 302 - $s;

    for ($y = 1; $y < $stop; $y++) {
        for ($x = 1; $x < $stop; $x++) {
            $area = calculateArea($x, $y, $s);
            //echo "{$x},{$y},{$s} : $area\n";
            if ($area > $current_max) {
                $current_max = $area;
                $current_key = "{$x},{$y},{$s}";
            }
            if ($current_max === $max_possible) {
                break 3;
            }
        }
    }
}



echo "FINAL $current_key : $current_max";