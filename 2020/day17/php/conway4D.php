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
                $grid[pos2key($x, $y, 0, 0)] = 1;
            }
        }
    }
    return [$size, $grid];
}

function pos2key(int $x, int $y, int $z, int $w): string {
    return sprintf('%03d,%03d,%03d,%03d', $x, $y, $z, $w);
}

function key2pos(string $key): array {
    return sscanf($key, '%03d,%03d,%03d,%03d');
}

function advance_grid(int $size, array $grid): array {
    static $seen = [];
    $orig_key = md5(json_encode($grid));
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
        ksort($next);
        $seen[$orig_key] = $next;
    }
    return $seen[$orig_key];
}

function inc_adj(string $poskey, array &$adjacent, int $size): void {
    $offsets = range(-1, 1);
    foreach ($offsets as $dx) {
        foreach ($offsets as $dy) {
            foreach ($offsets as $dz) {
                foreach ($offsets as $dw) {
                    if ($dx === 0 && $dy === 0 && $dz === 0 && $dw === 0) continue;
        
                    list($x, $y, $z, $w) = key2pos($poskey);
                    $x += $dx; $y += $dy; $z += $dz; $w += $dw;
                    $adj_key = pos2key($x, $y, $z, $w);
                    $adjacent[$adj_key] = ($adjacent[$adj_key] ?? 0) + 1;
                }
            }
        }
    }
}

list($size, $grid) = parse_grid($input);
ksort($grid);

for ($i = 0; $i < $iterations; $i++) { $grid = advance_grid($size, $grid); }

echo "Part 2: " . array_sum($grid) . "\n";
