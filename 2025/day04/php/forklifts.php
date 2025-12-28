<?php

$input = explode("\n", trim(file_get_contents($argv[1])));
$input_height = count($input);
$input_width = strlen($input[0]);

function pos2key(int $x, int $y): string {
    return sprintf('%03d,%03d', $x, $y);
}

function key2pos(string $key): array {
    return sscanf($key, '%03d,%03d');
}

function parse_grid(array $input): array {
    $grid = [];
    foreach ($input as $y => $line) {
        for ($x = 0; $x < strlen($line); $x++) {
            if ($line[$x] === '@') {
                $grid[pos2key($x, $y)] = 1;
            }
        }
    }
    return $grid;
}

function inc_adj(string $poskey, array &$adjacent, int $width, int $height): void {
    $offsets = [-1, 0, 1];
    foreach ($offsets as $dx) {
        foreach ($offsets as $dy) {
            if ($dx === 0 && $dy === 0) continue;

            list($x, $y) = key2pos($poskey);
            $x += $dx; $y += $dy;
            if ($x < 0 || $y < 0 || $x >= $width || $y >= $height) continue;
            $adjacent[pos2key($x, $y)] = ($adjacent[pos2key($x, $y)] ?? 0) + 1;
        }
    }
}

function find_blocked(array $grid, int $min_block, int $w, int $h): array {
    $adj = [];
    foreach ($grid as $key => $_) {
        inc_adj($key, $adj, $w, $h);
    }
    return array_filter(
        $adj,
        function($c) use ($min_block) { return $c >= $min_block;}
    );
}

$passes = 0;
$grid = parse_grid($input);
$removed = 0;

while(true) {
    $passes++;
    $blocked = find_blocked($grid, 4, $input_width, $input_height);
    $reachable = array_diff_key($grid, $blocked);
    if (count($reachable) === 0) break;

    $removed += count($reachable);
    $grid = array_diff_key($grid, $reachable);

    if ($passes === 1) {
        $p1 = $removed;
    }
}



echo "p1: {$p1}\n";
echo "p2: {$removed}\n";
