<?php

$signals = trim(file_get_contents($argv[1]));
$signals = array_map(
    function($packets) {
        return array_map(
            'json_decode',
            explode("\n", $packets)
        );
    },
    explode("\n\n", $signals)
);

$correct = 0;

$before_2 = 1;
$before_6 = 2;

function compare_tokens($a, $b): int {
    // both ints
    if (is_int($a) && is_int($b)) return $a <=> $b;

    // one not array
    if (is_int($a)) return compare_tokens([$a], $b);
    if (is_int($b)) return compare_tokens($a, [$b]);

    // both arrays
    $end = min(count($a),  count($b));
    for ($i = 0; $i < $end; $i++) {
        $cmp = compare_tokens($a[$i], $b[$i]);
        if ($cmp !== 0) return $cmp;
    }
    return count($a) <=> count($b);
}

foreach ($signals as $idx => $packets) {
    if (compare_tokens($packets[0], $packets[1]) < 1) $correct += $idx + 1;
    if ( compare_tokens($packets[0], [[2]]) < 0) {
        $before_2++;
        $before_6++;
    } elseif ( compare_tokens($packets[0], [[6]]) < 0) {
        $before_6++;
    }
    if ( compare_tokens($packets[1], [[2]]) < 0) {
        $before_2++;
        $before_6++;
    } elseif ( compare_tokens($packets[1], [[6]]) < 0) {
        $before_6++;
    }
}

$divs = $before_2 * $before_6;

echo "p1: {$correct} p2: {$divs}\n";