<?php

$input = file_get_contents($argv[1]);

// p1 is easy, p2 looks to require stream parsing
preg_match_all('#mul\((\d+),(\d+)\)#', $input, $m);

$score = 0;
foreach ($m[1] as $i => $a) {
    $b = $m[2][$i];
    $score += $a * $b;
}

echo "p1: {$score}\n";
