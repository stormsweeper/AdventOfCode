<?php

$length = intval($argv[1]);
$hasharray = range(0, $length -1);
$sequence = array_map('intval', explode(',', $argv[2]));
$skip = 0;
$pointer = 0;

function twist($step) {
    global $hasharray, $length, $pointer;
    $indices = array_map(
        function($index) use ($length) {
            return $index % $length;
        },
        range($pointer, $pointer + $step - 1)
    );

    $values = array_map(
        function($index) use ($hasharray) {
            return $hasharray[$index];
        },
        array_reverse($indices)
    );

    foreach ($indices as $vindex => $hindex) {
        $hasharray[$hindex] = $values[$vindex];
    }
}

foreach ($sequence as $step) {
    twist($step);
    $pointer = ($pointer + $step + $skip) % $length;
    $skip++;
}

echo $hasharray[0] * $hasharray[1];
