<?php

$scan = trim(file_get_contents($argv[1]));
$scan = explode("\n", $scan);

$grid_multi = intval($argv[2]??1);

define('SCAN_WIDTH', strlen($scan[0]));
define('SCAN_LENGTH', count($scan));
define('CAVERN_WIDTH',  SCAN_WIDTH  * $grid_multi);
define('CAVERN_LENGTH', SCAN_LENGTH * $grid_multi);

$scan = implode('', $scan);

function risklevel(string $pos): int {
    global $scan;
    [$x, $y] = key2pos($pos);
    $sx = $x % SCAN_WIDTH;
    $sy = $y % SCAN_LENGTH;
    $r = $scan[scanpos2i($sx,$sy)];
    $r += floor($x/SCAN_WIDTH);
    $r += floor($y/SCAN_LENGTH);
    return $r % 10;
}

function scanpos2i(int $x, int $y): string {
    return ($y * SCAN_WIDTH) + $x;
}

function pos2key(int $x, int $y): string {
    return "{$x},{$y}";
}

function key2pos(string $key): array {
    [$x,$y] = explode(',', $key);
    return [intval($x), intval($y)];
}

function adj(string $pos): array {
    [$x, $y] = key2pos($pos);
    $adj = [];
    foreach ([[-1,0], [1,0], [0,-1], [0,1]] as [$dx, $dy])  {
        $ax = $x + $dx; $ay = $y + $dy;
        if ($ax < 0 || $ay < 0 || $ax >= CAVERN_WIDTH || $ay >= CAVERN_LENGTH) {
            continue;
        }
        $adj[] = pos2key($ax, $ay);
    }
    return $adj;
}

$start = pos2key(0, 0);
$end   = pos2key(CAVERN_WIDTH - 1, CAVERN_LENGTH - 1);

$shortest = [];
$visited = [];
$consider = [0];
$queue = new SplPriorityQueue();
$queue->insert([$start, 0], PHP_INT_MAX);
$i = 0;
while (!isset($visited[$end]) || $queue->valid()) {
    [$node, $risk] = $queue->extract();

    // mark as visited
    $visited[$node] = 1;
    $next = [];
    foreach (adj($node) as $adj) {
        if (isset($visited[$adj])) continue;
        $adjrisk = $risk + risklevel($adj);
        $next[$adj] = min($adjrisk, $next[$adj]??PHP_INT_MAX);
    }
    foreach ($next as $n => $r) {
        $shortest[$n] = min($r, $shortest[$n]??PHP_INT_MAX);
        $queue->insert([$n, $r], PHP_INT_MAX - $r);
    }
}

// ksort($shortest);
// print_r($shortest);
echo $shortest[$end];
