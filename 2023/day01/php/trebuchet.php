<?php

$calibrations = fopen($argv[1], 'r');

$use_words = boolval($argv[2]??false);


$total = 0;
while (($cal = fgets($calibrations)) !== false) {
    if ($use_words) {
        $cal = strtr(
            $cal,
            [
                'one' => 1,
                'two' => 2,
                'three' => 3,
                'four' => 4,
                'five' => 5,
                'six' => 6,
                'seven' => 7,
                'eight' => 8,
                'nine' => 9,
                // these evil ones are throughtout the input
                'oneight' => 18,
                'twone' => 21,
                'threeight' => 38,
                'fiveight' => 58,
                'sevenine' => 79,
                'eightwo' => 82,
                'eighthree' => 83,
                'nineight' => 98,
            ]
        );
    }
    $cal = trim($cal, "abcdefghijklmnopqrstuvwxyz\n");
    $line = 0;
    if ($cal) {
        $line = $cal[0] * 10 + $cal[strlen($cal) - 1];
    }
    $total += $line;
}

echo $total . "\n";
