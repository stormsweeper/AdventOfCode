<?php

$jumps = file_get_contents($argv[1]);
$jumps = array_map('intval', explode("\n", $jumps));

$end = count($jumps);
$pointer = 0;
$count = 0;

while ($pointer < $end) {
    $next = $jumps[$pointer];
    $count++;
    if ($next >= 3) {
        $jumps[$pointer]--;
    } else {
        $jumps[$pointer]++;
    }
    $pointer += $next;
}

echo $count;