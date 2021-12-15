<?php

$cavern = trim(file_get_contents($argv[1]));
$cavern = explode("\n", $cavern);

define('CAVERN_WIDTH', strlen($cavern[0]));
define('CAVERN_LENGTH', count($cavern));

$cavern = implode('', $cavern);

function pos2i(int $x, int $y): string {
    return ($y * CAVERN_WIDTH) + $x;
}
function i2pos(int $i): array {
    return [$i%CAVERN_WIDTH, floor($i/CAVERN_WIDTH)];
}

function adj(int $i): array {
    list($x, $y) = i2pos($i);
    $adj = [];
    foreach ([[-1,0], [1,0], [0,-1], [0,1]] as [$dx, $dy])  {
        $ax = $x + $dx; $ay = $y + $dy;
        if ($ax < 0 || $ay < 0 || $ax >= CAVERN_WIDTH || $ay >= CAVERN_LENGTH) {
            continue;
        }
        $adj[] = pos2i($ax, $ay);
    }
    return $adj;
}

$shortest = [];

$consider = [0];

while ($consider) {
    $next = [];
    foreach ($consider as $start) {
        $sdist = $shortest[$start] ?? 0;
        foreach (adj($start) as $a) {
            $adist = $sdist + $cavern[$a];
            if ($adist > ($shortest[$a]??PHP_INT_MAX)) continue;
            $shortest[$a] = $adist;
            $next[$a] = $a;
        }
    }
    $consider = $next;
}

$end = pos2i(CAVERN_WIDTH - 1, CAVERN_LENGTH - 1);
echo $shortest[$end];

