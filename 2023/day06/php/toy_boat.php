<?php

$race_info = fopen($argv[1], 'r');

function parse_nums(string $nums): array {
    return array_values(array_map('intval', array_filter(explode(' ', $nums))));
}

$times = parse_nums(substr(fgets($race_info), 5));

$dists = parse_nums(substr(fgets($race_info), 9));

$num_games = count($times);

$moe = 1;

for ($g = 0; $g < $num_games; $g++) {
    $wins = 0;
    for ($v = 1; $v < $times[$g]; $v++) {
        $t = $times[$g] - $v;
        if ($v * $t > $dists[$g]) $wins++;
    }
    if ($wins) $moe *= $wins;
}

echo "p1: {$moe}\n";

$time = intval(implode('', $times));
$dist = intval(implode('', $dists));

$wins = 0;
for ($v = 1; $v < $time; $v++) {
    $t = $time - $v;
    if ($v * $t > $dist) $wins++;
}

echo "p2: {$wins}\n";
