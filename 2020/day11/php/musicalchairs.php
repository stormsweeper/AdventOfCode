<?php

$input = file_get_contents($argv[1]);
$iterations = intval($argv[2] ?? 0);

function parse_grid(string $map): array {
    $grid = [];
    $lines = explode("\n", trim($map));
    $size = count($lines);
    foreach ($lines as $y => $line) {
        for ($x = 0; $x < $size; $x++) {
            if ($line[$x] === 'L') {
                $grid[pos2key($x, $y)] = 0;
            }
            if ($line[$x] === '#') {
                $grid[pos2key($x, $y)] = 1;
            }
        }
    }
    return [$size, $grid];
}

function print_grid(int $size, array $grid): string {
    $out = '';
    foreach (range(0, $size - 1) as $y) {
        foreach (range(0, $size - 1) as $x) {
            $curr = $grid[pos2key($x, $y)] ?? null;
            if ($curr === 0) {
                $out .= 'L';
            } elseif ($curr === 1) {
                $out .= '#';
            } else {
                $out .= '.';
            }
        }
        $out .= "\n";
    }
    return $out;
}

function pos2key(int $x, int $y): string {
    return sprintf('%03d,%03d', $x, $y);
}

function key2pos(string $key): array {
    return sscanf($key, '%03d,%03d');
}

function advance_grid(int $size, array $grid): array {
    static $seen = [];
    $orig_key = md5(print_grid($size, $grid));
    if (!isset($seen[$orig_key])) {
        $next = $grid;
        $adjacent = [];
        foreach ($grid as $poskey => $occupied) {
            inc_adj($poskey, $adjacent, $size, $occupied);
        }
        foreach ($adjacent as $poskey => $level) {
            #echo "adj: {$poskey},{$level}\n";
            if (isset($grid[$poskey])) {
                if ($grid[$poskey] === 0) {
                    if ($level === 0) $next[$poskey] = 1;
                } else {
                    if ($level >= 4) $next[$poskey] = 0;
                }
            }
        }
        $seen[$orig_key] = $next;
    }
    return $seen[$orig_key];
}

function inc_adj(string $poskey, array &$adjacent, int $size, int $occupied): void {
    $offsets = range(-1, 1);
    foreach ($offsets as $dx) {
        foreach ($offsets as $dy) {
            if ($dx === 0 && $dy === 0) continue;

            list($x, $y) = key2pos($poskey);
            $x += $dx; $y += $dy;
            if ($x < 0 || $y < 0 || $x >= $size || $y >= $size) continue;
            $adjacent[pos2key($x, $y)] = ($adjacent[pos2key($x, $y)] ?? 0) + $occupied;
        }
    }
}

list($size, $grid) = parse_grid($input);

$last = null; $next = $grid;

while ($last !== $next) {
    $last = $next;
    $next = advance_grid($size, $next);
}

echo array_sum($last);