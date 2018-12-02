<?php

$boxids = array_map(
    'trim',
    file($argv[1])
);
sort($boxids);


for ($i = 1; $i < count($boxids); $i++) {
    $a = $boxids[$i - 1];
    $b = $boxids[$i];
    if (levenshtein($a, $b) === 1) {
        break;
    }
}

echo "{$a}\n{$b}\n";

echo implode('', array_intersect(str_split($a), str_split($b)));