<?php

$boxids = array_map(
    'trim',
    file($argv[1])
);

while(count($boxids) < strlen($boxids[0])) {
    $boxids = array_merge($boxids, $boxids);
}

$pair;
usort(
    $boxids,
    function($a, $b) use (&$fancy_ops, &$pair) {
        if (levenshtein($a, $b) === 1) {
            $pair = [$a, $b];
        }
        return $a <=> $b;
    }
);

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

