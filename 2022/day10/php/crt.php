<?php

$reg_X = 1;
$cycles = 0;
$sig_str = 0;
$screen = str_repeat('.', 240);

function tick() {
    global $reg_X, $cycles, $sig_str, $screen;
    $line_x = $cycles%40;
    if (abs($line_x - $reg_X) <= 1) {
        $screen[$cycles] = '#';
    } else {
        $screen[$cycles] = '.';
    }
    $cycles++;
    if ($cycles <= 220) {
        $check = $cycles - 20;
        if ($check%40 === 0) $sig_str += $cycles*$reg_X;
    }
}

$program = fopen($argv[1], 'r');
while (($inst = fgets($program)) !== false) {
    $inst = trim($inst);
    if (!$inst) continue;
    if ($inst === 'noop') {
        tick();
        continue;
    }
    // addx
    tick(); tick();
    $reg_X += intval(substr($inst, 5));
}

echo "sig str: {$sig_str}\n";

for ($l = 0; $l < 6; $l++) {
    echo substr($screen, $l * 40, 40);
    echo "\n";
}