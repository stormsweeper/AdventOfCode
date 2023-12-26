<?php

$patterns = array_map(
    function($p) { return explode("\n", $p); },
    explode("\n\n", trim(file_get_contents($argv[1])))
);

function hlof(array $pattern): int {
    echo implode("\n", $pattern) . "\n\n";
    $max_reflection = 0;
    $ymax =  count($pattern) - 1;
    $lof = 0;
    for ($y = 0; $y < $ymax; $y++) {
        if ($pattern[$y] !== $pattern[$y + 1]) continue;
        for ($ref = 1; $ref <= $ymax; $ref++) {
            echo "{$ref} > {$max_reflection} ({$y})\n";
            if ($y - $ref < 0 || $y + $ref > $ymax) break;
            echo "was not oob\n";
            if ($pattern[$y + 1 - $ref] !== $pattern[$y + $ref]) break;
            echo "was a reflection\n";
            if ($ref > $max_reflection) {
                echo "was > max ref\n";
                $max_reflection = $ref;
                $lof = $y + 1;
            } else {
                echo "was < max ref\n";
            }
        }
    }
    return $lof;
}

function vlof(array $pattern): int {
    // rotate 90deg
    $rotated = [];
    $xmax = strlen($pattern[0]);
    for ($x = 0; $x < $xmax; $x++) $rotated[] = vline($pattern, $x);
    return hlof($rotated);
}

function vline(array $pattern, int $x): string {
    $vl = '';
    foreach ($pattern as $p) $vl = $p[$x] . $vl;
    return $vl;
}

$sum = 0;

// echo hlof($patterns[1]) ."\n";
// exit;

foreach ($patterns as $i => $p) {
    $vlof = vlof($p);
    $hlof = hlof($p);
    echo "{$i}: v:{$vlof} h:{$hlof}\n\n";
    if ($vlof > $hlof) {
        $sum += $vlof;
    } elseif ($hlof > $vlof) {
        $sum += $hlof * 100;
    }
}

echo $sum;