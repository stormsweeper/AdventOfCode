<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

// p1
$total = 0;
foreach ($inputs as $line) {
    list($signals, $outputs) = explode(' | ', $line);
    foreach (explode(' ', $outputs) as $output) {
        $len = strlen($output);
        if ($len === 2 || $len === 4 || $len === 3 || $len === 7) $total++;
    }
}

echo $total;