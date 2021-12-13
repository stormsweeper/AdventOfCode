<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

function is_little(string $cave): bool {
    return strtolower($cave) === $cave;
}

$cave_passages = [];
foreach ($inputs as $line) {
    list($a, $b) = explode('-', $line);
    if (!isset($cave_passages[$a])) $cave_passages[$a] = [];
    if (!isset($cave_passages[$b])) $cave_passages[$b] = [];
    $cave_passages[$a][$b] = $b;
    $cave_passages[$b][$a] = $a;
}

function num_routes(string $cave, array $visited = []): int {
    global $cave_passages;

    if ($cave === 'end') return 1;
    if (isset($visited[$cave])) return 0;

    if (is_little($cave)) $visited[$cave] = true;

    $routes = 0;
    foreach ($cave_passages[$cave]??[] as $exit) {
        $routes += num_routes($exit, $visited);
    }
    return $routes;
}

echo num_routes('start');

