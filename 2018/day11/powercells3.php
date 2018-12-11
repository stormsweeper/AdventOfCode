<?php

$serial_id = intval($argv[1] ?? 8);


function powerLevel($x, $y) {
    global $serial_id;
    $rack_id = $x + 10;
    $l = ($rack_id * $y + $serial_id) * $rack_id;
    return floor(($l % 1000) / 100) - 5;
}

function calculatePos($x, $y) {
    global $grid;
    $key = "{$x},{$y}";
    if (!isset($grid[$key])) {
        $grid[$key] = powerLevel($x, $y);
    }
    return $grid[$key];
}

function calculateArea($sx, $sy, $size) {
    $a = 0;
    for ($dx = 0; $dx < $size; $dx++) {
        for ($dy = 0; $dy < $size; $dy++) {
            $a += calculatePos($sx + $dx, $sy + $dy);
        }
    }
    return $a;
}

$current_max = 0;
$current_key = null;


for ($s = 20; $s > 0; $s--) {
    $max_possible = $s * $s * 4;
    if ($max_possible < $current_max) {
        break;
    }
    $stop = 302 - $s;

    for ($y = 1; $y < $stop; $y++) {
        for ($x = 1; $x < $stop; $x++) {
            $area = calculateArea($x, $y, $s);
            echo "{$x},{$y},{$s} : $area\n";
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
