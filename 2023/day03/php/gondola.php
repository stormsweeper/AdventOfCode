<?php

$schematic = explode("\n", trim(file_get_contents($argv[1])));

$width = strlen($schematic[0]);

$sum = 0;
$gear_sum = 0;

function read_schematic(int $x, int $y): string {
    global $schematic;
    return $schematic[$y][$x] ?? '.';
}

function is_symbol(int $x, int $y): bool {
    $spot = read_schematic($x, $y);
    return $spot !== '.' && !is_numeric($spot);
}

function is_part_digit(int $x, int $y): bool {
    return 
        is_symbol($x - 1, $y) || is_symbol($x + 1, $y) || is_symbol($x, $y - 1) || is_symbol($x, $y + 1) || 
        is_symbol($x - 1, $y - 1) || is_symbol($x + 1, $y + 1) || is_symbol($x - 1, $y + 1) || is_symbol($x + 1, $y - 1)
    ;
}

function number_at(int $x, int $y): int {
    global $width;
    $num = '';
    $spot = read_schematic($x, $y);
    if (is_numeric($spot)) {
        $num = $spot;
        for ($lx = $x - 1; $lx > -1; $lx--) {
            $left = read_schematic($lx, $y);
            if (!is_numeric($left)) break;
            $num = $left . $num;
        }
        for ($rx = $x + 1; $rx < $width; $rx++) {
            $right = read_schematic($rx, $y);
            if (!is_numeric($right)) break;
            $num = $num . $right;
        }
    }
    return intval($num);
}

function gear_ratio(int $x, int $y): int {
    $spot = read_schematic($x, $y);
    if ($spot !== '*') return 0;

    $gears = [];
    foreach ([-1, 0, 1] as $dx) {
        foreach ([-1, 0, 1] as $dy) {
            if ($dx === 0 && $dy === 0) continue;
            $gear = number_at($x + $dx, $y + $dy);
            if ($gear) $gears[] = $gear;
        }
    }
    $gears = array_unique($gears);
    if (count($gears) !== 2) return 0;
    return array_product($gears);
}

foreach (array_keys($schematic) as $y) {
    $current_number = 0;
    $is_part_num = false;
    for ($x = 0; $x < $width; $x++) {
        $spot = read_schematic($x, $y);
        if (is_numeric($spot)) {
            $current_number = $current_number*10 + $spot;
            $is_part_num = $is_part_num || is_part_digit($x, $y);
        } else {
            if ($current_number > 0) {
                if ($is_part_num) $sum += $current_number;
                $current_number = 0;
                $is_part_num = false;
            }
            $gear_sum += gear_ratio($x, $y);
        }
    }
    if ($is_part_num) $sum += $current_number;
}

echo "p1: {$sum}\np2: {$gear_sum}\n";