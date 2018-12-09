<?php

$inst = file_get_contents($argv[1]);
$end = strlen($inst);
$current_floor = 0;

for ($i = 0; $i < $end; $i++) {
    if ($inst[$i] === '(') {
        $current_floor++;
    }
    if ($inst[$i] === ')') {
        $current_floor--;
    }
    if ($current_floor === -1) {
        break;
    }
}

echo $i + 1;