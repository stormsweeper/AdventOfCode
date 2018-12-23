<?php

$input = array_filter(array_map('trim', file($argv[1])));

// pos=<0,0,0>, r=4
function mapBots($line) {
    $r = '/^pos=<(?<x>-?\d+),(?<y>-?\d+),(?<z>-?\d+)>, r=(?<radius>-?\d+)$/';
    preg_match($r, $line, $m);
    return [
        'pos' => [intval($m['x']), intval($m['y']), intval($m['z'])],
        'radius' => intval($m['radius']),
    ];
}

$bots = array_map('mapBots', $input);

usort(
    $bots,
    function($a, $b) {
        return $b['radius'] <=> $a['radius'];
    }
);

$big_bot = $bots[0];

$in_range = array_filter(
    $bots,
    function($bot) use ($big_bot) {
        [$x1, $y1, $z1] = $bot['pos'];
        [$x2, $y2, $z2] = $big_bot['pos'];
        return (abs($x1 - $x2) + abs($y1 - $y2) + abs($z1 - $z2)) <= $big_bot['radius'];
    }
);

echo count($in_range);