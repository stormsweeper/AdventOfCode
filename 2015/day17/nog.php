<?php
$containers = trim(file_get_contents($argv[1]));
$containers = array_map('intval', explode("\n", $containers));

$target_total = intval($argv[2] ?? 0);

rsort($containers);

function try_containers(int $target, array $containers, &$possible = [], array $used = []) {
    if (count($used) === 1) {
        echo json_encode([$target, $containers, $used]) . "\n";        
    }
    // look for exact match
    if ($target === 0) {
        // add exact to used set
        // flag as possible
        ksort($used);
        $possible[ json_encode($used) ] = count($used);
        return;
    }

    // else look for highest available
    $usable = array_filter($containers, function($c) use ($target) { return $c <= $target; });
    foreach ($usable as $i => $c) {
        // if found, add and recurse
        $next_cnt = $usable;
        $next_used = $used;
        $next_used[$i] = $c;
        unset($next_cnt[$i]);
        try_containers($target - $c, $next_cnt, $possible, $next_used);
    }
        // else end/skip
}

try_containers($target_total, $containers, $poss);

echo "Part 1: " . count($poss) . "\n";

$min = min($poss);
$pmin = 0;
foreach ($poss as $n) {
    $pmin += ($n === $min);
}

echo "Part 2: {$pmin}\n";