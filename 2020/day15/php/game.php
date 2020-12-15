<?php

$starting = trim($argv[1]);
$starting = explode(',', $starting);
$starting = array_map('intval', $starting);
$starting_len = count($starting);

$rounds = intval($argv[2]);

$spoken_on = [];
$turn = 0;
$last_number = null;

function next_number(): int {
    global $starting, $starting_len, $spoken_on, $turn, $last_number;
    $turn++;
    if ($turn <= $starting_len) {
        $next = $starting[$turn - 1];
    } else {
        $last_i = array_key_last($spoken_on[$last_number]);
        if ($last_i === 0) {
            $next = 0;
        } else {
            $next = $spoken_on[$last_number][$last_i] - $spoken_on[$last_number][$last_i - 1];
        }
    }
    if (!isset($spoken_on[$next])) {
        $spoken_on[$next] = [];
    }
    $spoken_on[$next][] = $turn;
    return $last_number = $next;
}

while ($turn < $rounds) {
    #$last = $last_number ?? '-';
    $next = next_number();
    #echo "Output:\t{$turn}\t{$last}\t{$next}\n";
}

echo "Last spoken: {$last_number}\n";
