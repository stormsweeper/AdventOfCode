<?php

$input = file_get_contents($argv[1]);
$iterations = intval($argv[2] ?? 0);

function parse_grid(string $map): array {
    $grid = [];
    $lines = explode("\n", trim($map));
    $size = count($lines);
    foreach ($lines as $y => $line) {
        for ($x = 0; $x < $size; $x++) {
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
            if (!empty($grid[pos2key($x, $y)])) {
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

function advance_grid(int $size, array $grid, bool $corners_stuck = false): array {
    static $seen = [];
    if ($corners_stuck) {
        $grid[pos2key(0, 0)] = 1;
        $grid[pos2key(0, $size - 1)] = 1;
        $grid[pos2key($size - 1, $size - 1)] = 1;
        $grid[pos2key($size - 1, 0)] = 1;            
    }
    $orig_key = md5(print_grid($size, $grid) . var_export($corners_stuck, true));
    if (!isset($seen[$orig_key])) {
        $next = $adjacent = [];
        foreach ($grid as $poskey => $_) {
            inc_adj($poskey, $adjacent, $size);
        }
        foreach ($adjacent as $poskey => $level) {
            if (!empty($grid[$poskey])) {
                if ($level === 2 || $level == 3) {
                    $next[$poskey] = 1;
                }
            }
            elseif ($level === 3) {
                $next[$poskey] = 1;
            }
        }
        if ($corners_stuck) {
            $next[pos2key(0, 0)] = 1;
            $next[pos2key(0, $size - 1)] = 1;
            $next[pos2key($size - 1, $size - 1)] = 1;
            $next[pos2key($size - 1, 0)] = 1;            
        }
        $seen[$orig_key] = $next;
    }
    return $seen[$orig_key];
}

function inc_adj(string $poskey, array &$adjacent, int $size): void {
    $offsets = range(-1, 1);
    foreach ($offsets as $dx) {
        foreach ($offsets as $dy) {
            if ($dx === 0 && $dy === 0) continue;

            list($x, $y) = key2pos($poskey);
            $x += $dx; $y += $dy;
            if ($x < 0 || $y < 0 || $x >= $size || $y >= $size) continue;
            $adjacent[pos2key($x, $y)] = ($adjacent[pos2key($x, $y)] ?? 0) + 1;
        }
    }
}

list($size, $grid) = parse_grid($input);

for ($i = 0; $i < $iterations; $i++) { $grid = advance_grid($size, $grid); }

echo "Part 1: " . array_sum($grid) . "\n";

list($size, $grid) = parse_grid($input);

for ($i = 0; $i < $iterations; $i++) { $grid = advance_grid($size, $grid, true); /*echo print_grid($size, $grid) . "---\n";*/ }

echo "Part 2: " . array_sum($grid) . "\n";



