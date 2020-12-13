<?php

$inputs = trim(file_get_contents($argv[1]));
list($etd, $routes) = explode("\n", $inputs);
$routes = explode(',', $routes);

$operating = array_filter($routes, function($r) {return $r !== 'x';});

$best_time = 2*$etd;
$best_route = 0;
foreach ($routes as $route) {
    if ($route === 'x') continue;
    $dep = ceil($etd / $route) * $route;
    if ($dep < $best_time) {
        $best_time = $dep;
        $best_route = $route;
    }
}

$score = ($best_time - $etd) * $best_route;
echo "Part 1: {$score}\n";