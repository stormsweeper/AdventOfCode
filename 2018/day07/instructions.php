<?php

$instructions = file($argv[1]);

$steps = [];
foreach ($instructions as $inst) {
    if (strlen($inst) < 36) {
        continue;
    }
    $req = $inst[5];
    $steps[$req] = $steps[$req] ?? [];
    $dep = $inst[36];
    $steps[$dep] = $steps[$dep] ?? [];

    $steps[$dep][$req] = 1;
}

$completed = [];

function nextCompleted($steps) {
    $ready = array_filter(
        $steps,
        function($reqs) {
            global $completed;
            return empty(array_diff_key($reqs, $completed));
        }
    );
    ksort($ready);
    return (array_keys($ready))[0];
}

while (count($steps)) {
    $next = nextCompleted($steps);
    $completed[$next] = 1;
    unset($steps[$next]);
}

echo implode(array_keys($completed));