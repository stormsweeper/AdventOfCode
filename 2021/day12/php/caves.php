<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

$allow_second_look = ($argv[2]??'') === 'true';

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

function num_routes(string $cave, array $visited = [], bool $allow_second_look = false, bool $had_second_look = false): int {
    global $cave_passages;

    if (isset($visited[$cave])) {
        if (!$allow_second_look || $had_second_look) return 0;
        $had_second_look = true;
    }

    if (is_little($cave)) $visited[$cave] = true;

    $routes = 0;
    foreach ($cave_passages[$cave]??[] as $exit) {
        if ($exit === 'start') continue;
        if ($exit === 'end') {
            $routes++;
            continue;
        }
        $routes += num_routes($exit, $visited, $allow_second_look, $had_second_look);
    }
    return $routes;
}

echo num_routes('start', [], $allow_second_look);

