<?php

$input = fopen($argv[1], 'r');

$splits = 0;

$line = fgets($input);
$startx = strpos($line, 'S');
$beams = array_fill(0, strlen($line), 0);
$beams[$startx] = 1;

while (($line = fgets($input)) !== false) {
    $next = $beams;
    foreach ($beams as $x => $bc) {
        if ($bc < 1) continue;
        if ($line[$x] === '^') {
            $splits++;
            $next[$x - 1] += $bc;
            $next[$x + 1] += $bc;
            $next[$x] = 0;
        }
    }
    $beams = $next;

}

echo "p1: {$splits}\n";

$p2 = array_sum($beams);

echo "p2: {$p2}\n";
