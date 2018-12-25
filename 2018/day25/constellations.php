<?php

ini_set('memory_limit', '1G');

$stars = array_filter(array_map('trim', file($argv[1])));
$stars = array_map(
    function($s) {
        return array_map('intval', explode(',', $s));
    },
    $stars
);

function pos2key($pos) {
    return implode(',', $pos);
}

function key2pos($key) {
    return array_map('intval', explode(',', $key));
}

function manhattanDistance($posA, $posB) {
    [$x1, $y1, $z1, $t1] = $posA;
    [$x2, $y2, $z2, $t2] = $posB;
    return abs($x1 - $x2) + abs($y1 - $y2) + abs($z1 - $z2) + abs($t1 - $t2);
}

$dists = [];

foreach ($stars as $star) {
    $s_key = pos2key($star);
    $dists[$s_key] = [];
    foreach ($stars as $star2) {
        $s_key2 = pos2key($star2);
        $dist = manhattanDistance($star, $star2);
        if ($dist <= 3) {
            $dists[$s_key][] = $s_key2;
        }
    }
}

$mapped = [];

function getConnected($start, $carry = []) {
    global $dists;
    $unlinked = array_diff($dists[$start], $carry);
    $carry = array_unique(array_merge($unlinked, $carry));
    foreach ($unlinked as $u) {
        $carry = array_merge($carry, getConnected($u, $carry));
    }
    return array_unique($carry);
}

foreach (array_keys($dists) as $root) {
    if (isset($mapped[$root])) {
        continue;
    }
    $mapped[$root] = $root;
    foreach (getConnected($root) as $c) {
        $mapped[$c] = $root;
    }
}

echo count(array_count_values($mapped));