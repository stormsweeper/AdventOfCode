<?php

require_once __DIR__ . '/knothashclass.php';

function bitSum(int $value) {
    $sum = 0;
    while ($value) {
        // e.g. for 2
        // $value = 2 & 1; // 
        $value &= ($value - 1);
        $sum++;
    }
    return $sum;
}

$input = $argv[1];
$size = 128;
$used = 0;

for ($i = 0; $i < $size; $i++) {
    $kh = new KnotHash("{$input}-{$i}");
    $sums = array_map('bitSum', $kh->compactHash());
    $used += array_sum($sums);
}

echo $used;