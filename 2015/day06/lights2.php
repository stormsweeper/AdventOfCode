<?php

$instructions = array_filter(array_map('parseInstruction', file($argv[1])));

function parseInstruction($line) {
    // turn on 0,0 through 999,999
    $r = '/(?<action>turn on|turn off|toggle) (?<min_x>\d+),(?<min_y>\d+) through (?<max_x>\d+),(?<max_y>\d+)/';
    if (preg_match($r, $line, $m)) {
        return $m;
    }
    return [];
}

$lights = [];

function adjustLight($x, $y, $val) {
    global $lights;
    if (!isset($lights[$x])) {
        $lights[$x] = [];
    }
    $lights[$x][$y] = max(0, ($lights[$x][$y] ?? 0) + $val);
}

foreach ($instructions as $inst) {
    for ($x = $inst['min_x']; $x <= $inst['max_x']; $x++) {
        for ($y = $inst['min_y']; $y <= $inst['max_y']; $y++) {
            switch ($inst['action']) {
                case 'turn on':
                    adjustLight($x, $y, 1);
                    break;

                case 'turn off':
                    adjustLight($x, $y, -1);
                    break;

                case 'toggle':
                    adjustLight($x, $y, 2);
                    break;
            }
        }
    }
}


echo array_sum(array_map('array_sum', $lights));