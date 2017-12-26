<?php

$input = fopen($argv[1], 'r');

$progs = [];

function processLine($line) {
    $prog = [];
    $regex = '/^(?P<name>[a-z]+) \((?P<weight>\d+)\)(?: -> (?P<children>[a-z, ]+))?/';
    if (preg_match($regex, $line, $matches)) {
        $prog['name'] = $matches['name'];
        $prog['weight'] = intval($matches['weight']);
        $prog['children'] = array_filter(explode(', ', $matches['children'] ?? ''));
        $prog['parent'] = null;
    }
    return $prog;
}

function totalWeight($prog) {
    global $progs;
    $child_weights = array_map(
        function($name) use ($progs) {
            return totalWeight($progs[$name]);
        },
        $prog['children']
    );
    return $prog['weight'] + array_sum($child_weights);
}


while (($line = fgets($input)) !== false) {
    $prog = processLine($line);
    $progs[ $prog['name'] ] = $prog;
}

foreach ($progs as $name => &$prog) {
    $prog['child_weights'] =  array_map(
        function($child) use ($progs) {
            return totalWeight($progs[$child]);
        },
        $prog['children']
    );
    foreach ($prog['children'] as $child) {
        $progs[$child]['parent'] = $name;
    }
}

$top = null;
foreach ($progs as $name => $prog) {
    if (!$prog['parent']) {
        $top = $prog;
        break;
    }
}

print_r($progs['vrgxe']);
