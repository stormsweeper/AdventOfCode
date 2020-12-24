<?php

$input = trim(file_get_contents($argv[1]));
$input = explode("\n", $input);

function pos2key(int $q, int $r): string {
    return sprintf('%03d,%03d', $q, $r);
}

function key2pos(string $key): array {
    return sscanf($key, '%03d,%03d');
}

$floor = [];

# Had to google how to do hex math as I had forgotten (other than knowing it was sorta kinda doing 3d math)
# This solution brought to you by https://www.redblobgames.com/grids/hexagons/

foreach ($input as $line) {
    preg_match_all('#[ns]?[ew]#', $line, $matches);
    $q = $r = 0;
    foreach ($matches[0] as $dir) {
        if ($dir === 'w') {
            $q -= 1;
        }
        if ($dir === 'e') {
            $q += 1;
        }
        if ($dir === 'nw') {
            $r -= 1;
        }
        if ($dir === 'ne') {
            $q += 1;
            $r -= 1;
        }
        if ($dir === 'sw') {
            $q -= 1;
            $r += 1;
        }
        if ($dir === 'se') {
            $r += 1;
        }
    }
    $key = pos2key($q, $r);
    $floor[$key] = (int)!($floor[$key] ?? 0);
}

$floor = array_filter($floor);
ksort($floor);

$p1 = array_sum($floor);
echo "Part 1: {$p1}\n";

function advance_grid(array $grid): array {
    static $seen = [];
    $orig_key = md5(json_encode($grid));
    if (!isset($seen[$orig_key])) {
        $next = $adjacent = [];
        foreach ($grid as $poskey => $_) {
            inc_adj($poskey, $adjacent);
        }
        foreach ($adjacent as $poskey => $level) {
            if (!empty($grid[$poskey])) {
                if ($level === 1 || $level == 2) {
                    $next[$poskey] = 1;
                }
            }
            elseif ($level === 2) {
                $next[$poskey] = 1;
            }
        }
        ksort($next);
        $seen[$orig_key] = $next;
    }
    return $seen[$orig_key];
}

function inc_adj(string $poskey, array &$adjacent): void {
    $offsets = range(-1, 1);
    foreach ($offsets as $dq) {
        foreach ($offsets as $dr) {
            if ($dq === $dr) continue;

            list($q, $r) = key2pos($poskey);
            $q += $dq; $r += $dr;
            $adj_key = pos2key($q, $r);
            $adjacent[$adj_key] = ($adjacent[$adj_key] ?? 0) + 1;
        }
    }
}

for ($i = 0; $i < 100; $i++) { $floor = advance_grid($floor); }

$p2 = array_sum($floor);
echo "Part 2: {$p2}\n";

