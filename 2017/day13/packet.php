<?php

$input = file_get_contents($argv[1]);
$input = explode("\n", $input);

$firewall = [];
$maxdepth = 0;

foreach ($input as $line) {
    list ($depth, $range) = explode(': ', $line);
    $depth = intval($depth);
    $firewall[$depth] = intval($range);
    $maxdepth = max($maxdepth, $depth);
}

function atDelay($delay) {
    global $firewall, $maxdepth;
    $cost = 0;
    $caught = false;
    for ($t = 0; $t <= $maxdepth; $t++) {
        if (!isset($firewall[$t])) {
            continue;
        }
    
        $scanfreq = 2 * ($firewall[$t] - 1);
        if (($t + $delay)%$scanfreq === 0) {
            $caught = true;
            $cost += $firewall[$t] * $t;
        }
    }
    return [$caught, $cost];
}

$delay = 0;
$caught = true;
while (atDelay($delay)[0]) {
    $delay++;
}

echo $delay;