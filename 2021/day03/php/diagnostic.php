<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);
$word_size = strlen($inputs[0]);

$sums = [];
foreach ($inputs as $word) {
    for ($bit = 0; $bit < $word_size; $bit++) {
        $sums[$bit] = ($sums[$bit] ?? 0) + $word[$bit];
    }
}

$half = count($inputs)/2;
$gamma = $epsilon = '00000';

foreach ($sums as $bit => $sum) {
    if ($sum > $half) {
        $gamma[$bit] = 1;
        $epsilon[$bit] = 0;
    }
    else {
        $gamma[$bit] = 0;
        $epsilon[$bit] = 1;
    }
}

$consumption = bindec($gamma) * bindec($epsilon);

echo $consumption;