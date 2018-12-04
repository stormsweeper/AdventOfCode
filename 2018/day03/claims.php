<?php

$fabric = [];
$claimed = [];

// returns an array with id, x, y, w, h keys
function parseClaim($line) {
    preg_match('/#(?<id>\d+) @ (?<x>\d+),(?<y>\d+): (?<w>\d+)x(?<h>\d+)/', $line, $m);
    return array_filter(
        $m,
        function($k) {
            return !is_numeric($k);
        },
        ARRAY_FILTER_USE_KEY
    );
    return $m;
}

function makeCoords($x, $y) {
    return "{$x},{$y}";
}

function stakeClaim($line) {
    global $fabric, $claimed;
    $claim = parseClaim($line);
    $claimed[ $claim['id'] ] = [];
    for ($wd = 0; $wd < $claim['w']; $wd++) {
        for ($hd = 0; $hd < $claim['h']; $hd++) {
            $coords = makeCoords($claim['x'] + $wd, $claim['y'] + $hd);
            $fabric[$coords] = ($fabric[$coords] ?? 0) + 1;
            $claimed[ $claim['id'] ][$coords] = 1;
        }
    }
    
}

foreach (file($argv[1]) as $line) {
    stakeClaim($line);
}

$overclaimed = array_filter(
    $fabric,
    function($claims) {
        return $claims > 1;
    }
);

echo count($overclaimed) . "\n";

foreach ($claimed as $id => $claim) {
    $overlap = array_intersect_key($overclaimed, $claim);
    if (count($overlap) === 0) {
        echo $id;
    }
}