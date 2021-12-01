<?php

$depths = fopen($argv[1], 'r');

$last_single = 0;
$changes_single = 0;

$idx = 0;
$window = [];
$last_triple = $changes_triple = 0; $last_changed = false;

while (($depth = fgets($depths)) !== false) {
    $depth = intval($depth);
    if ($last_single && $last_single < $depth) {
        $changes_single++;
    }
    $last_single = $depth;
    // part 2
    $window[$idx] = $depth;
    if (count($window) === 3) {
        $triple = array_sum($window);
        if ($last_triple && $last_triple < $triple) {
            $changes_triple++;
            $last_changed = true;
        } else {
            $last_changed = false;
        }
        $last_triple = $triple;
    }
    $idx = ($idx + 1) % 3;
}

// drop if the last window wasn't comlete
if ($idx < 2 && $last_changed) $changes_triple--;

echo "single depth changes: {$changes_single}\n";
echo "window depth changes: {$changes_triple}\n";

