<?php

$readings = trim(file_get_contents($argv[1]));

$readings = array_map('parse_nums', explode("\n", $readings));

function parse_nums(string $nums): array {
    return array_values(array_map('intval', explode(' ', $nums)));
}

function outer_vals(array $reading, int $depth = 0): array {
    // echo json_encode($reading) . "\n";
    if (count(array_unique($reading)) === 1) return [$reading[0], $reading[0]];
    $lower = [];
    for ($i = 1; $i < count($reading); $i++) {
        $lower[] = $reading[$i] - $reading[$i - 1];
    }
    [$lower_prev, $lower_next] = outer_vals($lower, $depth + 1);
    $prev_val = $reading[0] - $lower_prev;
    $next_val = $reading[$i - 1] + $lower_next;
    // echo "next: {$lower_val}\n\n";
    return [$prev_val, $next_val];
}

$sum_prev = $sum_next = 0;
foreach ($readings as $reading) {
    [$prev, $next] = outer_vals($reading);
    $sum_prev += $prev;
    $sum_next += $next;
}

echo "p1: {$sum_next}\np2: {$sum_prev}\n";

