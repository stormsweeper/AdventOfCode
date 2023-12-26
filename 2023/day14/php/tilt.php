<?php

$platform = explode("\n", trim(file_get_contents($argv[1])));

$load = 0;

// rotate 90° right, so "N" is now east
for ($x = 0; $x < strlen($platform[0]); $x++) {
    $r_row = '';
    foreach ($platform as $row) {
        $r_row = $row[$x] . $r_row;
    }
    $load += sort_and_count($r_row);
}

function sort_and_count(string $row): int {
    $row = explode('#', $row);
    // . is < O in ASCII/UTF-8
    foreach ($row as &$chunk) {
        $chunk = str_split($chunk);
        sort($chunk);
        $chunk = implode($chunk);
    }
    $row = implode('#', $row);
    $sum = 0;
    for ($i = 0; $i < strlen($row); $i++) {
        if ($row[$i] === 'O') $sum += $i + 1;
    }
    return $sum;
}


echo $load;
