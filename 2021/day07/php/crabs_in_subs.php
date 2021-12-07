<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode(',', $inputs);

$crabs_at_hpos = [];

$total_crabs = 0;
foreach ($inputs as $hpos) {
    $crabs_at_hpos[$hpos] = ($crabs_at_hpos[$hpos]??0) + 1;
    $total_crabs++;
}

uksort(
    $crabs_at_hpos,
    function ($hpos_a, $hpos_b) use ($crabs_at_hpos) {
        // sort first in descending qty
        $cmpdist = $crabs_at_hpos[$hpos_b] <=> $crabs_at_hpos[$hpos_a];
        // if eq, then sort in ascending horizontal pos
        if ($cmpdist === 0) {
            return $hpos_a <=> $hpos_b;
        }
        return $cmpdist;
    }
);

$fuel = 0;
$focus = -1;
foreach ($crabs_at_hpos as $hpos => $num_crabs) {
    if ($focus === -1) $focus = $hpos;
    $fuel += abs($hpos - $focus) * $num_crabs;
}

echo $fuel;