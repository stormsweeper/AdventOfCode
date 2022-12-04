<?php

$assignments = fopen($argv[1], 'r');

function rangify(string $s): array {
    [$min, $max] = explode('-', $s);
    return [intval($min), intval($max)];
}

$p1 = $p2 = 0;
while (($assignment = fgets($assignments)) !== false) {
    $assignment = trim($assignment);
    if (!$assignment) continue;
    [$a, $b] = explode(',', $assignment);
    $a = rangify($a);
    $b = rangify($b);

    // b is in a
    if ($a[0] <= $b[0] && $a[1] >= $b[1]) {
        $p1++;
        $p2++;
    } 
    // a is in b
    elseif ($b[0] <= $a[0] && $b[1] >= $a[1]) {
        $p1++;
        $p2++;
    }
    // start of a is in b
    elseif ($b[0] <= $a[0] && $b[1] >= $a[0]) {
        $p2++;
    }
    // start of b is in a
    elseif ($a[0] <= $b[0] && $a[1] >= $b[0]) {
        $p2++;
    }
    // end of a is in b
    elseif ($b[0] <= $a[1] && $b[1] >= $a[1]) {
        $p2++;
    }
    // end of b is in a
    elseif ($a[0] <= $b[1] && $a[1] >= $b[1]) {
        $p2++;
    }
}

echo "p1:{$p1} p2:{$p2}\n";