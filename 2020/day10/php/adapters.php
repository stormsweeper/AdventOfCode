<?php

$adapters = trim(file_get_contents($argv[1]));
$adapters = array_map('intval', explode("\n", $adapters));
// add on our termini
$adapters[] = $end = max($adapters) + 3;
$adapters[] = 0;
sort($adapters);

$diffs = array_fill(1, 3, 0);
$paths = [1];

for ($i = 1; $i < count($adapters); $i++) {
    $diff = $adapters[$i] - $adapters[$i - 1];
    //echo json_encode([$last, $adapters[$i], $diff]) . "\n";
    $diffs[$diff]++;
    $paths[$adapters[$i]] = $paths[$adapters[$i - 1]];
    if ($adapters[$i] - ($adapters[$i - 2] ?? -4) <= 3) {
        $paths[$adapters[$i]] += $paths[$adapters[$i - 2]];
    }
    if ($adapters[$i] - ($adapters[$i - 3] ?? -4) <= 3) {
        $paths[$adapters[$i]] += $paths[$adapters[$i - 3]];
    }
}

//print_r($diffs);
$part1 = $diffs[1] * ($diffs[3]);
echo "Part 1: {$part1}\n";

//print_r($paths);
echo "Part 2: {$paths[$end]}\n";