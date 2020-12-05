<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

function parse_pass(string $pass): array {
    $rows = range(0,127); $aisles = range(0,7);
    for ($i = 0; $i < strlen($pass); $i++) {
        if ($pass[$i] === 'F') {
            $rows = array_chunk($rows, count($rows)/2)[0];
            continue;
        }
        if ($pass[$i] === 'B') {
            $rows = array_chunk($rows, count($rows)/2)[1];
            continue;
        }
        if ($pass[$i] === 'L') {
            $aisles = array_chunk($aisles, count($aisles)/2)[0];
            continue;
        }
        if ($pass[$i] === 'R') {
            $aisles = array_chunk($aisles, count($aisles)/2)[1];
            continue;
        }
    }
    return [$pass, $rows[0], $aisles[0], $rows[0] * 8 + $aisles[0]];
}

$passes = array_map('parse_pass', $inputs);

usort(
    $passes,
    function($a, $b) {
        return $b[3] <=> $a[3];
    }
);

$highest_seat = $passes[0][3];
$occupied = array_reverse(
    array_map(
        function($a) {
            return $a[3];
        },
        $passes
    )
);

$all_seats = range(min($occupied), max($occupied));
$empty_seats = array_values(array_diff($all_seats, $occupied));
echo "Highest seat ID: {$highest_seat}\n";
echo "My seat is {$empty_seats[0]}\n";


