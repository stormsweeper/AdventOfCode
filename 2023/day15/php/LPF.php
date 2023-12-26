<?php

$steps = explode(',', trim(file_get_contents($argv[1])));

function lpf_hash(string $s): int {
    $hash = 0;
    for ($i = 0; $i < strlen($s); $i++) {
        $hash += ord($s[$i]);
        $hash *= 17;
        $hash %= 256;
    }
    return $hash;
}

assert(lpf_hash('HASH') === 52);

$sum = 0;

foreach ($steps as $s) $sum += lpf_hash($s);

echo $sum;