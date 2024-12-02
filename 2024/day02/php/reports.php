<?php

$input = fopen($argv[1], 'r');

$left = $right = [];

$num_safe = $with_dampening = 0;
while (($line = fgets($input)) !== false) {
    $report = array_map('intval', explode(' ', $line));
    if (is_safe($report)) {
        $num_safe++;
        $with_dampening++;
    } elseif (safe_with_dampening($report)) {
        $with_dampening++;
    }
}

echo "p1: {$num_safe}\n";
echo "p1: {$with_dampening}\n";

function is_safe(array $report): bool {
    return (increasing($report) || decreasing($report)) && in_range($report);
}

function increasing(array $report): bool {
    $last = 0;
    foreach ($report as $val) {
        if ($val <= $last) return false;
        $last = $val;
    }
    return true;
}

function decreasing(array $report): bool {
    $last = PHP_INT_MAX;
    foreach ($report as $val) {
        if ($val >= $last) return false;
        $last = $val;
    }
    return true;
}

function in_range(array $report): bool {
    for ($i = 0; $i < count($report) - 1; $i++) {
        $diff = abs($report[$i] - $report[$i + 1]);
        if ($diff < 1 || $diff > 3) return false;
    }
    return true;
}

function safe_with_dampening(array $report): bool {
    for ($i = 0; $i < count($report); $i++) {
        $copy = $report;
        unset($copy[$i]);
        $copy = array_values($copy);
        if (is_safe($copy)) return true;
    }
    return false;
}