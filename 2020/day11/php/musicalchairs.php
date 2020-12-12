<?php

$input = file_get_contents($argv[1]);
$iterations = intval($argv[2] ?? 0);

function parse_seating(string $map): array {
    $seated = $chairs = [];
    $lines = explode("\n", trim($map));
    $size = count($lines);
    foreach ($lines as $y => $line) {
        for ($x = 0; $x < $size; $x++) {
            if ($line[$x] === 'L') {
                $chairs[pos2key($x, $y)] = 1;
            }
            if ($line[$x] === '#') {
                $seated[pos2key($x, $y)] = 1;
                $chairs[pos2key($x, $y)] = 1;
            }
        }
    }
    return [$size, $chairs, $seated];
}

function print_grid(int $size, array $chairs, array $seated): string {
    $out = '';
    foreach (range(0, $size - 1) as $y) {
        foreach (range(0, $size - 1) as $x) {
            $curr = $grid[pos2key($x, $y)] ?? null;
            if (!empty($seated[pos2key($x, $y)])) {
                $out .= '#';
            } elseif (!empty($chairs[pos2key($x, $y)])) {
                $out .= 'L';
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

function advance_grid(int $size, array $chairs, array $seated, bool $useLOS = false): array {
    static $seen = [];
    $orig_key = md5(print_grid($size, $chairs, $seated));
    if (isset($seen[$orig_key])) {
        return $seen[$orig_key];
    }
    $next = $seated;
    $adjacent = [];
    foreach ($seated as $poskey => $_) {
        inc_adj($poskey, $adjacent, $size, $chairs, $useLOS);
    }
    foreach ($chairs as $poskey => $_) {
        #echo "adj: {$poskey},{$level}\n";
        if (empty($adjacent[$poskey])) {
            #echo "filling seat {$poskey}\n";
            $next[$poskey] = 1;
        } elseif ($adjacent[$poskey] >= ($useLOS ? 5 : 4)) {
            #echo "vacating seat {$poskey}\n";
            $next[$poskey] = 0;
        }
    }
    return $seen[$orig_key] = array_filter($next);
}

function inc_adj(string $poskey, array &$adjacent, int $size, array $chairs, bool $useLOS = false): void {
    $offsets = range(-1, 1);
    foreach ($offsets as $dx) {
        foreach ($offsets as $dy) {
            if ($dx === 0 && $dy === 0) continue;

            list($x, $y) = key2pos($poskey);
            $x += $dx; $y += $dy;
            if (oob($size, $x, $y)) continue;
            $nextpos = pos2key($x, $y);
            $adjacent[$nextpos] = ($adjacent[$nextpos] ?? 0) + 1;
            if ($useLOS) {
                while (!oob($size, $x, $y) && empty($chairs[pos2key($x, $y)])) {
                    $x += $dx; $y += $dy;
                    if (oob($size, $x, $y)) break;
                    $nextpos = pos2key($x, $y);
                    $adjacent[$nextpos] = ($adjacent[$nextpos] ?? 0) + 1;
                }
            }
        }
    }
}

function oob(int $size, int $x, int $y): bool {
    return ($x < 0 || $y < 0 || $x >= $size || $y >= $size);
}

list($size, $chairs, $seated) = parse_seating($input);

$last = null; $next = $seated;

while ($last !== $next) {
    $last = $next;
    $next = advance_grid($size, $chairs, $next);
    #echo print_grid($size, $chairs, $next); echo "--\n--\n\n";
}

echo "Part 1: " . array_sum($last) . "\n";


list($size, $chairs, $seated) = parse_seating($input);
$last = null; $next = $seated;

while ($last !== $next) {
    $last = $next;
    $next = advance_grid($size, $chairs, $next, true);
    #echo print_grid($size, $chairs, $next); echo "--\n--\n\n";
}
echo "Part 2: " . array_sum($last) . "\n";
