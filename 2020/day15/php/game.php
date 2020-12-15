<?php


#  ¯\_(ツ)_/¯
ini_set('memory_limit', '2G');

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
        if (count($spoken_on[$last_number]) === 1) {
            $next = 0;
        } else {
            $next = $spoken_on[$last_number][1] - $spoken_on[$last_number][0];
        }
    }
    if (!isset($spoken_on[$next])) {
        $spoken_on[$next] = [$turn];
    } else {
        $spoken_on[$next] = [$spoken_on[$next][1] ?? $spoken_on[$next][0], $turn];
    }
    return $last_number = $next;
}

$spoken = [];
while ($turn < $rounds) {
    #$last = $last_number ?? '-';
    $next = next_number();
    #echo "Output:\t{$turn}\t{$last}\t{$next}\n";
}

echo "Last spoken: {$last_number}\n";
