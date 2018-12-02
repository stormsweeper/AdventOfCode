<?php

$adjustments = file($argv[1]);
$num_adj = count($adjustments);
$freqs = [0 => 1];
$current = 0;


for ($i = 0; $i < $num_adj * 10000; $i++) {
    $current += intval($adjustments[ $i % $num_adj ]);
    if (isset($freqs[$current])) {
        echo $current;
        exit;
    }
    $freqs[$current] = 1;
}
echo 'WTF'. $i;