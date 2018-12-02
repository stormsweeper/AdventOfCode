<?php

$bufferlen = 1;
$step = intval($argv[1]);
$iterations = intval($argv[2] ?? 2017);

$pos = $insert = 0;
$oneval = -1;

while ($insert < $iterations) {
    // add step, modulo length
    $pos = ($pos + $step) % $bufferlen + 1;
    $insert++;
    $bufferlen++;
    if ($pos === 1) {
        $oneval = $insert;
    }
}
echo $oneval;


