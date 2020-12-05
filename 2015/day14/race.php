<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

$elapsed = intval($argv[2] ?? 0);

$reindeer_team = [];

foreach ($inputs as $line) {
    preg_match('#(?P<reindeer>\w+) can fly (?P<speed>\d+) km/s for (?P<ftime>\d+) seconds, but then must rest for (?P<rtime>\d+) seconds#', $line, $m);
    $reindeer_team[$m['reindeer']] = $m;
}

function best_distances(int $elapsed) {
    global $reindeer_team;
    $distances = [];
    foreach ($reindeer_team as $reindeer => $stats) {
        $cycle_time = $stats['ftime'] + $stats['rtime'];
        $cycles = floor($elapsed / $cycle_time);
        $remaining_time = min($stats['ftime'], $elapsed%$cycle_time);
        $distances[$reindeer] = $stats['speed'] * ($cycles * $stats['ftime'] + $remaining_time);
    }
    $best_dist = max($distances);
    return array_filter($distances, function($d) use ($best_dist) {return $d === $best_dist;});
}

echo json_encode(best_distances($elapsed), JSON_PRETTY_PRINT);

$points = [];
foreach (range(1, $elapsed) as $tick) {
    foreach (best_distances($tick) as $reindeer => $_) {
        $points[$reindeer] = ($points[$reindeer] ?? 0) + 1;
    }
}

arsort($points);

echo json_encode($points, JSON_PRETTY_PRINT);