<?php
ini_set('memory_limit',-1);
$input = fopen($argv[1], 'r');

$pipes = [];

while (($line = fgets($input)) !== false) {
    list ($lh, $rh) = explode(' <-> ', $line);
    $procs = array_map('intval', explode(',', $rh));
    $procs[] = intval($lh);
    foreach ($procs as $outer) {
        if (!isset($pipes[$outer])) {
            $pipes[$outer] = [$outer];
        }
        foreach ($procs as $inner) {
            $pipes[$outer][] = $inner;
        }
        $pipes[$outer] = array_unique($pipes[$outer]);
    }
}

function getConnections($pos, $carry = []) {
    global $pipes;
    if (empty($pipes[$pos])) {
        return [];
    }

    $connected = $pipes[$pos];
    $subs = array_diff($connected, $carry, [$pos]);
    foreach ($subs as $sub) {
        $newcarry = array_merge($carry, $connected);
        $also = getConnections($sub, $newcarry);
        $connected = array_unique(array_merge($connected, $also));
    }
    return array_unique($connected);
}


$groups = [0 => getConnections(0)];
$ids = array_keys($pipes);
sort($ids);
while ($ids) {
    $id = array_shift($ids);
    $groups[$id] = getConnections($id);
    $ids = array_diff($ids, $groups[$id]);
}

print_r([count($groups[0]), count($groups)]);
