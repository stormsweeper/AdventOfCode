<?php

$instructions = file($argv[1]);
$num_elves = intval($argv[2] ?? 1);
$min_time = intval($argv[3] ?? 0);

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
$in_progress = [];
$elapsed = 0;

function advanceTime() {
    global $in_progress, $elapsed;
    $ticks = min($in_progress ?: [0]);
    //$ticks = 3;
    //echo "ticks: {$ticks}\n";
    $elapsed += $ticks;
    $in_progress = array_map(
        function($timer) use ($ticks) {
            return $timer - $ticks;
        },
        $in_progress
    );
}

function completeSteps() {
    global $completed, $in_progress, $steps, $elapsed;
    foreach (array_keys($in_progress) as $step) {
        if ($in_progress[$step] < 1) {
            $completed[$step] = 1;
            unset($in_progress[$step]);
            echo "{$elapsed}: completed step {$step}\n";
            printQueue();
        }
    }
}

function nextReady() {
    global $steps;
    $ready = array_filter(
        $steps,
        function($reqs) {
            global $completed;
            return empty(array_diff_key($reqs, $completed));
        }
    );
    ksort($ready);
    return array_keys($ready);
}

function workersAvailable() {
    global $in_progress, $num_elves;
    return count($in_progress) < $num_elves;
}

function timeForStep($step) {
    global $min_time;
    return ord($step) - 64 + $min_time;
}

function scheduleNext() {
    global $in_progress, $elapsed, $steps;
    if (!workersAvailable()) {
        return;
    }
    $next_steps = nextReady();
    while (workersAvailable() && count($next_steps)) {
        $step = array_shift($next_steps);
        $in_progress[$step] = timeForStep($step);
        unset($steps[$step]);
        echo "{$elapsed}: scheduling step {$step} for {$in_progress[$step]} secs\n";
        printQueue();
    }
    
}

function printQueue() {
    global $completed, $in_progress, $elapsed, $steps;
    echo sprintf(
        "%s: complete: %s working: %s waiting: %s\n",
        $elapsed,
        json_encode(array_keys($completed)),
        json_encode(array_keys($in_progress)),
        json_encode(array_keys($steps))
    );
}

while (count($steps) || count($in_progress)) {
    // advance time
    advanceTime();
    // check for completed
    completeSteps();
    // check for next
    // schedule next
    scheduleNext();
}

echo $elapsed;
