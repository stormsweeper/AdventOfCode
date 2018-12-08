<?php

$boxids = array_map(
    'trim',
    file($argv[1])
);

$pair = null;

foreach ($boxids as $a) {
    foreach ($boxids as $b) {
        if (levenshtein($a, $b) === 1) {
            $pair = [$a, $b];
            break 2;
        }
    }
}

$common = array_map(
    function($a, $b) {
        if ($a === $b) {
            return $a;
        }
        return false;
    },
    str_split($pair[0]),
    str_split($pair[1])
);
echo "{$pair[0]}\n{$pair[1]}\n";
echo implode('', array_filter($common));

