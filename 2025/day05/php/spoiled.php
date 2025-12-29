<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n\n", $inputs);

$on_hand = array_map('intval', explode("\n", $inputs[1]));

$fresh_ranges = [];
foreach (explode("\n", $inputs[0]) as $line) {
    $fresh_ranges[] = array_map('intval', explode('-', $line));
}

usort(
    $fresh_ranges, function($a, $b){
        $cmp = $a[0] <=> $b[0];
        if ($cmp === 0) {
            return $a[1] <=> $b[1];
        }
        return $cmp;
    }
);

$compacted = [];
foreach ($fresh_ranges as [$min, $max]) {
    for ($i = 0; $i < count($compacted); $i++) {
        if ($min >= $compacted[$i][0] && $min <= $compacted[$i][1] + 1) {
            $compacted[$i][1] = max($compacted[$i][1], $max);
            continue 2;
        }
    }
    $compacted[] = [$min, $max];
}


$p1 = 0;

foreach ($on_hand as $ing) {
    foreach ($compacted as [$min, $max]) {
        if ($ing >= $min && $ing <= $max) {
            $p1++;
            continue 2;
        }
    }
}

echo "p1: {$p1}\n";

$p2 = 0;
foreach ($compacted as [$min, $max]) {
    $p2 += $max - $min + 1;
}
echo "p2: {$p2}\n";
