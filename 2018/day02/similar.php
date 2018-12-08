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

$common = array_map(
    function($a, $b) {
        if ($a === $b) {
            return $a;
        }
        return false;
    },
    str_split($a),
    str_split($b)
);
echo implode('', array_filter($common));