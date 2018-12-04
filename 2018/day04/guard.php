<?php

$log = array_map(
    'trim',
    file($argv[1])
);
sort($log);

function parseLine($line) {
    $regex = '/\[[\d\-]+ (?P<hour>\d+):(?P<min>\d+)\] (?:Guard #(?P<guard>\d+) )?(?P<action>begins shift|falls asleep|wakes up)/';
    preg_match($regex, $line, $m);
    return $m;
}

$shifts = [];
function logSleep($guard, $start, $end) {
    global $shifts;
    if (!isset($shifts[$guard])) {
        $shifts[$guard] = [];
    }

    foreach (range($start, $end) as $min) {
        $shifts[$guard][$min] = ($shifts[$guard][$min] ?? 0) + 1;
    }
}

$guard = 0;
$sleep_start = 0;
$sleep_end = 0;
foreach ($log as $line) {
    $data = parseLine($line);
    if ($data['action'] == 'begins shift') {
        $guard = $data['guard'];
    }
    elseif ($data['action'] == 'falls asleep') {
        $sleep_start = intval($data['min']);
    }
    elseif ($data['action'] == 'wakes up') {
        $sleep_end = intval($data['min']) - 1;
        logSleep($guard, $sleep_start, $sleep_end);
    }
}

// reverse sort by total mins
uasort(
    $shifts,
    function($a, $b) {
        return array_sum($b) - array_sum($a);
    }
);
$best_guard = (array_keys($shifts))[0];

// reverse sort by most freq mins
uasort(
    $shifts[$best_guard],
    function($a, $b) {
        return $b - $a;
    }
);
$best_min = (array_keys($shifts[$best_guard]))[0];

$score = $best_guard * $best_min;

echo "{$best_guard} {$best_min} {$score}";
