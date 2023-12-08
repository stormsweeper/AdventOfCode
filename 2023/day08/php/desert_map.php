<?php

$mapdata = fopen($argv[1], 'r');

$turns = trim(fgets($mapdata));

// skip the blank line
fgets($mapdata);

$nodes = [];
while (($line = fgets($mapdata)) !== false) {
    preg_match('/(\w{3}) = [(](\w{3}), (\w{3})[)]/', $line, $m);
    [, $from, $left, $right] = $m;
    $nodes[$from] = ['L' => $left, 'R' => $right];
    
}

$steps = 0;
$at = 'AAA';

if (isset($nodes['AAA']) && isset($nodes['AAA'])) {
    while ($at !== 'ZZZ') {
        $next_turn = $turns[$steps % strlen($turns)];
        $steps++;
        $was = $at;
        $at = $nodes[$at][$next_turn];
    }
}

echo "p1: {$steps}\n";

$paths = $ends = $dists = [];

foreach (array_keys($nodes) as $n) {
    if ($n[2] === 'A') $paths[$n] = $n;
    if ($n[2] === 'Z') $ends[$n] = $n;
}

$steps = 0;


while ($paths) {
    $next = $paths;
    $next_turn = $turns[$steps % strlen($turns)];
    $steps++;
    foreach ($paths as $path_start => $path_at) {
        $next[$path_start] = $to = $nodes[$path_at][$next_turn];
        if ($to[2] === 'Z') {
            unset($next[$path_start]);
            $dists[$path_start] = $steps;
        }
    }

    $paths = $next;
}

$lcm = array_reduce($dists, 'gmp_lcm', 1);

echo "p2: {$lcm}\n";
