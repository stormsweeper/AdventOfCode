<?php

$boxids = array_map(
    'trim',
    file($argv[1])
);

foreach ($boxids as $a) {
    foreach ($boxids as $b) {
        if (levenshtein($a, $b) === 1) {
            break 2;
        }
    }
}

echo "\n{$a}\n{$b}\n";

echo implode('', array_intersect(str_split($a), str_split($b)));