<?php

$input = fopen($argv[1], 'r');

function max_jolt(string $bank, int $cells, int $start = 0, int $sum = 0): int {
    $cells--;
    $end = max(strlen($bank) - $cells, $start);

    $max = 0;
    $next_start = -1;
    for ($i = $start; $i < $end; $i++) {
        $cj = intval($bank[$i]);
        if ($cj > $max) {
            $max = $cj;
            $next_start = $i + 1;
            if ($max === 9) break;
        }
    }

    $sum = $sum*10 + $max;

    if ($cells === 0) return $sum;

    return max_jolt($bank, $cells, $next_start, $sum);
}

$sump1 = $sump2 = 0;
while (($line = fgets($input)) !== false) {
    $bank = trim($line);
    $max = strlen($bank);
    $joltage = 0;

    $sump1 += max_jolt($bank, 2);
    $sump2 += max_jolt($bank, 12);
}

echo "p1: {$sump1}\n";
echo "p2: {$sump2}\n";
