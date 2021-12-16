<?php

$scan = trim(file_get_contents($argv[1]));
$scan = explode("\n", $scan);

$grid_multi = intval($argv[2]??1);

define('SCAN_WIDTH', strlen($scan[0]));
define('SCAN_LENGTH', count($scan));
define('CAVERN_WIDTH',  SCAN_WIDTH  * $grid_multi);
define('CAVERN_LENGTH', SCAN_LENGTH * $grid_multi);

$scan = implode('', $scan);

class CavernNode {
    // manhattan distance to end
    private $end_dist = 0;

    public function __construct(public string $key, public float $risk) {
        [$x, $y] = key2pos($key);
        $this->end_dist = (CAVERN_WIDTH - 1 - $x) + (CAVERN_LENGTH - 1 - $y);
    }

    public function weight(): float {
        return $this->risk + $this->end_dist;
    }
}

function sortNodes(CavernNode $a, CavernNode $b): int {
    // reverse sorting as pop is faster than shift
    return $b->weight() <=> $a->weight();
}

function risklevel(string $pos): int {
    global $scan;
    [$x, $y] = key2pos($pos);
    $sx = $x % SCAN_WIDTH;
    $sy = $y % SCAN_LENGTH;
    $r = $scan[scanpos2i($sx,$sy)];
    $r += floor($x/SCAN_WIDTH);
    $r += floor($y/SCAN_LENGTH);
    while ($r > 9) $r -= 9;
    return $r;
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

function priority(string $frompos, string $topos): int {
    global $shortest;
    [$to_x, $to_y] = pos2key($topos);
    $manhattan = (CAVERN_WIDTH - 1 - $to_x) + (CAVERN_LENGTH - 1 - $to_y);
}

$start = new CavernNode(pos2key(0, 0), 0);
$end   = new CavernNode(pos2key(CAVERN_WIDTH - 1, CAVERN_LENGTH - 1), INF);

$visited = [];
$consider = [
    $start->key => $start,
    $end->key   => $end,
];

do {
    uasort($consider, 'sortNodes');

    // get the shortest node/path, then get neighbors
    $node = array_pop($consider);

    // check risks for all neighbors
    foreach (adj($node->key) as $adj) {
        // if we already cleared the node, skip on
        if (isset($visited[$adj])) continue;

        $consider[$adj] = $consider[$adj] ?? new CavernNode($adj, INF);
        
        $consider[$adj]->risk = min($consider[$adj]->risk, $node->risk + risklevel($adj));
    }

    // mark as visited
    $visited[$node->key] = $node->risk;
} while ($consider);

echo $end->risk;
