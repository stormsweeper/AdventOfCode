<?php

$input = trim(file_get_contents($argv[1]));

$ranges = array_map(
    function($r) {
        [$s, $e] = explode('-', $r);
        return [intval($s), intval($e)];
    },
    explode(',', $input)
);

function p1_invalid(int $num): bool {
    return preg_match('/^(\d+)\1$/', strval($num));
}

function p2_invalid(int $num): bool {
    return preg_match('/^(\d+)\1+$/', strval($num));
}

$p1 = $p2 = 0;

foreach ($ranges as [$start, $end]) {
    for ($i = $start; $i <= $end; $i++) {
        if (p1_invalid($i)) $p1 += $i;
        if (p2_invalid($i)) $p2 += $i;
    }
}

echo "p1: {$p1}\n";
echo "p2: {$p2}\n";
