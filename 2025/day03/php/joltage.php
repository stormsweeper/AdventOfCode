<?php

$input = fopen($argv[1], 'r');

$sum = 0;
while (($line = fgets($input)) !== false) {
    $bank = trim($line);
    $max = strlen($bank);
    $joltage = 0;

    for ($i = 0; $i < $max - 1; $i++) {
        for ($j = $i + 1; $j < $max; $j++) {
            $combo = intval("{$bank[$i]}{$bank[$j]}");
            $joltage = max($joltage, $combo);
            if ($joltage == 99) break 2;
        }
    }

    $sum += $joltage;
}

echo "p1: {$sum}\n";