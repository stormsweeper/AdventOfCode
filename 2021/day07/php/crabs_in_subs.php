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
arsort($crabs_at_hpos);

$p1_best_cost = $p2_best_cost = PHP_INT_MAX;
$dist_sums = [];

for ($hpos_a = 0; $hpos_a <= $max_hpos; $hpos_a++) {
    $p1_cost = $p2_cost = 0;
    foreach ($crabs_at_hpos as $hpos_b => $num) {
        $dist = abs($hpos_b - $hpos_a);

        // part 1
        if ($p1_cost < $p1_best_cost) $p1_cost += $dist * $num;

        // part 2
        if ($p2_cost < $p2_best_cost) {
            // this caching shaves a few millis
            if (!isset($dist_sums[$dist])) {
                $dist_sums[$dist] = $dist / 2 * ($dist + 1);
            }
            $p2_cost += $dist_sums[$dist] * $num;
        }
        if ($p1_cost > $p1_best_cost && $p2_cost > $p2_best_cost) continue 2;
    }
    $p1_best_cost = min($p1_best_cost, $p1_cost);
    $p2_best_cost = min($p2_best_cost, $p2_cost);
}

echo "p1:{$p1_best_cost} p2:{$p2_best_cost}\n";
