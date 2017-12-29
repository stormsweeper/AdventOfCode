<?php

$steps = [
    'n' => 0,
    'ne' => 0,
    'nw' => 0,
    's' => 0,
    'se' => 0,
    'sw' => 0,
];


function currentDist($steps) {
    $oppos = [
        'n' => 's',
        'ne' => 'sw',
        'nw' => 'se',
    ];
    $netdirs = [
        // a + b -> c
        ['ne', 'nw', 'n'],
        ['se', 'sw', 's'],
        ['n', 'se', 'ne'],
        ['n', 'sw', 'nw'],
        ['s', 'ne', 'se'],
        ['s', 'nw', 'sw'],
    ];

    foreach ($oppos as $dir => $oppo) {
        $net = min($steps[$dir], $steps[$oppo]);
        $steps[$dir]  -= $net;
        $steps[$oppo] -= $net;
    }
    foreach ($netdirs as list($a, $b, $c)) {
        $net = min($steps[$a], $steps[$b]);
        $steps[$a] -= $net;
        $steps[$b] -= $net;
        $steps[$c] += $net;
    }

    return array_sum($steps);
}

$input = file_get_contents('input.txt') ?? '';
//$input = 'se,sw,se,sw,sw';
$input = explode(',', $input);

$maxdist = 0;

// take the basic steps
foreach ($input as $dir) {
    $steps[$dir]++;
    $maxdist = max($maxdist, currentDist($steps));
}

print_r([currentDist($steps), $maxdist]);
