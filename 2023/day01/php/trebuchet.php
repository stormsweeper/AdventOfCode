<?php

$calibrations = fopen($argv[1], 'r');

$use_words = !empty($argv[2]);

$words = [
    'one' => 1,
    'two' => 2,
    'three' => 3,
    'four' => 4,
    'five' => 5,
    'six' => 6,
    'seven' => 7,
    'eight' => 8,
    'nine' => 9,
];

$total = 0;
while (($cal = fgets($calibrations)) !== false) {
    if ($use_words) {
        $digits = '';
        for ($i = 0; $i < strlen($cal); $i++) {
            if (is_numeric($cal[$i])) {
                $digits .= $cal[$i];
                continue;
            }
            // words can overlap like 'eighttwo'
            foreach ($words as $w => $d) {
                if (strpos($cal, $w, $i) === $i) {
                    $digits .= $d;
                    continue 2;
                } 
            }
        }
    }
    $cal = trim($cal, "abcdefghijklmnopqrstuvwxyz\n");
    $line = 0;
    if ($cal) {
        $line = $cal[0] * 10 + $cal[strlen($cal) - 1];
    }
    $total += $line;
}

echo $total . "\n";
