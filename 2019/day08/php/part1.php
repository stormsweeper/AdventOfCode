<?php

$input = trim(file_get_contents($argv[1]));
$width = intval($argv[2]);
$height = intval($argv[3]);


$layers = str_split($input, $width * $height);

$fewest_zeroes = PHP_INT_MAX;
$checksum = null;

foreach ($layers as $layer) {
    $counts = count_chars($layer);
    $zero_count = $counts[ord('0')];
    if ($zero_count < $fewest_zeroes) {
        $fewest_zeroes = $zero_count;
        $checksum = $counts[ord('1')] * $counts[ord('2')];
    }
}

echo $checksum;