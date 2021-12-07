<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode(',', $inputs);

// map the count at each pos
// [hpos => num]
$crabs_at_hpos = [];
$max_hpos = 0;
foreach ($inputs as $hpos) {
    $max_hpos = max($max_hpos, $hpos);
    $crabs_at_hpos[$hpos] = ($crabs_at_hpos[$hpos]??0) + 1;
}

$best_cost = PHP_INT_MAX;
$best_pos = -1;

for ($hpos_a = 0; $hpos_a <= $max_hpos; $hpos_a++) {
    $cost = 0;
    foreach ($crabs_at_hpos as $hpos_b => $num) {
        $cost += abs($hpos_b - $hpos_a) * $num;
        if ($cost > $best_cost) continue 2;
    }
    $best_cost = $cost;
    $best_pos = $hpos_a;
}

$p1_cost = $best_cost;

// part 2

$best_cost = PHP_INT_MAX;
$best_pos = -1;

for ($hpos_a = 0; $hpos_a <= $max_hpos; $hpos_a++) {
    $cost = 0;
    foreach ($crabs_at_hpos as $hpos_b => $num) {
        $dist = abs($hpos_b - $hpos_a);
        $dist_cost = 0;
        for ($d = 0; $d <= $dist; $d++) $dist_cost += $d;
        $cost += $dist_cost * $num;
        if ($cost > $best_cost) continue 2;
    }
    $best_cost = $cost;
    $best_pos = $hpos_a;
}

$p2_cost = $best_cost;

echo "p1:{$p1_cost} p2:{$p2_cost}\n";