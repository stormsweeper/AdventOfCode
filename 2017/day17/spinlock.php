<?php

$buffer = [0];
$step = intval($argv[1]);

$pos = $insert = 0;

while ($insert < 2017) {
    // add step, modulo length
    $pos = ($pos + $step) % count($buffer) + 1;

    // insert next after
    $head = array_slice($buffer, 0, $pos);
    $tail = array_slice($buffer, $pos);
    $buffer = array_merge($head, [++$insert], $tail);
}

$npos = ($pos + 1) % 2018;
echo $buffer[$npos];

