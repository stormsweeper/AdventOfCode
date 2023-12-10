<?php

$readings = trim(file_get_contents($argv[1]));

$readings = array_map('parse_nums', explode("\n", $readings));

function parse_nums(string $nums): array {
    return array_values(array_map('intval', explode(' ', $nums)));
}

function next_val(array $reading, int $depth = 0): int {
    // echo json_encode($reading) . "\n";
    if (count(array_unique($reading)) === 1) return $reading[0];
    $next = [];
    for ($i = 1; $i < count($reading); $i++) {
        $next[] = $reading[$i] - $reading[$i - 1];
    }
    $next_val = $reading[$i - 1] + next_val($next, $depth + 1);
    // echo "next: {$next_val}\n\n";
    return $next_val;
}

$sum = array_sum(array_map('next_val', $readings));

echo "p1: {$sum}\n";

