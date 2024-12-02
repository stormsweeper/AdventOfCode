<?php

$input = fopen($argv[1], 'r');

$left = $right = [];

$num_safe = 0;
while (($line = fgets($input)) !== false) {
    $report = array_map('intval', explode(' ', $line));
    // echo json_encode($report) . "\n";
    if ( (increasing($report) || decreasing($report)) && in_range($report) ) {
        // echo "safe\n";
        $num_safe++;
    } else {
        // echo "unsafe\n";
    }
}

echo "p1: {$num_safe}\n";


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
