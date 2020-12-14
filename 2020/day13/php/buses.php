<?php

$inputs = trim(file_get_contents($argv[1]));
list($etd, $routes) = explode("\n", $inputs);
$routes = explode(',', $routes);

function closest(int $interval, int $min): int {
    return ceil($min / $interval) * $interval;
}

$best_time = 2*$etd;
$best_route = 0;
foreach ($routes as $route) {
    if ($route === 'x') continue;
    $dep = closest($route, $etd);
    if ($dep < $best_time) {
        $best_time = $dep;
        $best_route = $route;
    }
}

$score = ($best_time - $etd) * $best_route;
echo "Part 1: {$score}\n";

// shamelessly cribbed from @lizthegrey
$operating = array_filter($routes, function($r) {return $r !== 'x';});
$time = 0;
$product = 1;

foreach ($operating as $offset => $route) {
    while (($time + $offset)%$route !== 0) {
        $time += $product;
    }
    $product *= $route;
}

echo "Part 2: {$time}\n";
