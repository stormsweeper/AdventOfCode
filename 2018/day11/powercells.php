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

function calculateArea($sx, $sy) {
    $a = 0;
    for ($dx = 0; $dx < 3; $dx++) {
        for ($dy = 0; $dy < 3; $dy++) {
            $a += calculatePos($sx + $dx, $sy + $dy);
        }
    }
    return $a;
}

$max_possible = 39; // 9x4
$current_max = 0;
$current_key = null;



yrot: for ($y = 1; $y < 299; $y++) {
    xrot: for ($x = 1; $x < 299; $x++) {
        $area = calculateArea($x, $y);
        if ($area > $current_max) {
            $current_max = $area;
            $current_key = "{$x},{$y}";
        }
        if ($current_max === $max_possible) {
            break 2;
        }
    }
}

echo $current_key;