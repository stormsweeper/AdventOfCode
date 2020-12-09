<?php

$ciphertext = trim(file_get_contents($argv[1]));
$ciphertext = array_map('intval', explode("\n", $ciphertext));
$scanlen = intval($argv[2]);


$sums = [];

for ($pos = $scanlen; $pos < count($ciphertext); $pos++) {
    $current = $ciphertext[$pos];
    $operands = array_slice($ciphertext, $pos - $scanlen, $scanlen);
    foreach ($operands as $a) {
        if ($a >= $current) continue;
        foreach ($operands as $b) {
            if ($b >= $current || $a === $b) continue;
            if ($a + $b === $current) continue 3;
        }
    }
    break;
}

echo "Part 1: {$current}\n";

for ($i = 0; $i < $pos - 1; $i++) {
    for ($len = 2; $len <= $pos - $i; $len++) {
        $operands = array_slice($ciphertext, $i, $len);
        if (array_sum($operands) === $current) {
            break 2;
        }
    }
}

$weakness = min($operands) + max($operands);

echo "Part 2: {$weakness}\n";