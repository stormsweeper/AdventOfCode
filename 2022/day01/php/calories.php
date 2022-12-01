<?php

$cals_list = fopen($argv[1], 'r');

$current_cals = 0;

$elves = [];

while (($cals = fgets($cals_list)) !== false) {
    $cals = trim($cals);
    if ($cals === '') {
        // check
        $elves[] = $current_cals;
        $current_cals = 0;
        continue;
    }
    // add to current
    $current_cals += intval($cals);
}

rsort($elves);

$most = $elves[0];
$top3 = $elves[0] + $elves[1] + $elves[2];

echo "most: {$most} top3: {$top3}\n";