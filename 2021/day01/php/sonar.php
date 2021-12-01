<?php

$depths = fopen($argv[1], 'r');

$last = 0;
$changes = 0;

while (($depth = fgets($depths)) !== false) {
    $depth = intval($depth);
    if ($last && $last < $depth) {
        $changes++;
    }
    $last = $depth;
}

echo "changes: {$changes}\n";

