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

// well, p2 is also just a regex, but using a diff match order
$cmd_regex = '#mul\((\d+),(\d+)\)|do\(\)|don\'t\(\)#';
preg_match_all($cmd_regex, $input, $cmds, PREG_SET_ORDER);

$enabled = true;
$score2 = 0;
foreach ($cmds as $cmd) {
    if ($cmd[0] === 'do()') {
        $enabled = true;
        continue;
    }
    if ($cmd[0] === 'don\'t()') {
        $enabled = false;
        continue;
    }

    if (!$enabled) continue;

    $score2 += $cmd[1] * $cmd[2];
}

echo "p2: {$score2}\n";
