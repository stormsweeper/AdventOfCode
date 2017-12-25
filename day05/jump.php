<?php

$jumps = file_get_contents($argv[1]);
$jumps = array_map('intval', explode("\n", $jumps));

$end = count($jumps);
$pointer = 0;
$count = 0;

while ($pointer < $end) {
    $next = $jumps[$pointer];
    $jumps[$pointer]++;
    $count++;
    $pointer += $next;
}

echo $count;