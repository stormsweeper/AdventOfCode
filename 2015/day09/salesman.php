<?php

$target = 2020;
$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

$matrix = [];

foreach ($inputs as $line) {
    list($a, , $b, , $dist) = explode(' ', $line);
    $matrix = array_merge_recursive(
        $matrix,
        [
            $a => [$b => intval($dist)],
            $b => [$a => intval($dist)],
        ]
    );
}

$stops = array_keys($matrix);

function calculate_route($route, $matrix) {
    $dist = 0;
    for ($i = 0; $i < count($route) - 1; $i++) {
        $dist += $matrix[ $route[$i] ][ $route[$i + 1] ];
    }
    return $dist;
}

function path_key($route) {
    return implode(' -> ', $route);
}

$paths = [path_key($stops) => calculate_route($stops, $matrix)];
$max = gmp_fact(count($stops));

while (count($paths) < $max) {
    shuffle($stops);
    $key = path_key($stops);
    if (!isset($paths[$key])) {
        $paths[$key] = calculate_route($stops, $matrix);
    }
}

asort($paths);

echo "Shortest: " . array_shift($paths) . "\n";
echo "Longest: " . array_pop($paths) . "\n";