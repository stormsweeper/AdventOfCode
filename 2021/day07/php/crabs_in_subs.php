<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode(',', $inputs);

// map the count at each pos
// [hpos => num]
$crabs_at_hpos = [];
foreach ($inputs as $hpos) {
    $crabs_at_hpos[$hpos] = ($crabs_at_hpos[$hpos]??0) + 1;
}

// reverse sort the map by counts (e.g. in example 2 would be the first key)
arsort($crabs_at_hpos);

$best_cost = PHP_INT_MAX;
$best_pos = -1;

foreach ($crabs_at_hpos as $hpos_a => $_) {
    $cost = 0;
    foreach ($crabs_at_hpos as $hpos_b => $num) {
        $cost += abs($hpos_b - $hpos_a) * $num;
        if ($cost > $best_cost) continue 2;
    }
    $best_cost = $cost;
    $best_pos = $hpos_a;
}

echo $best_cost;