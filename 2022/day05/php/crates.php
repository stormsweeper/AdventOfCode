<?php

$input = fopen($argv[1], 'r');

$stacks = [];
$num_stacks = 0;

// parse crates
while (($tier = fgets($input)) !== false) {
    $tier = substr($tier, 0, -1);
    if (!$tier) break;

    if (empty($stacks)) {
        $num_stacks = ceil(strlen($tier)/4);
        $stacks = array_fill(1, $num_stacks, '');
    }

    for ($i = 1; $i <= $num_stacks; $i++) {
        $idx = ($i - 1) * 4 + 1;
        $crate = $tier[$idx];
        if ($crate === ' ') continue;
        if ($crate === '1') break;
        $stacks[$i] .= $crate;
    }
}

// move crates
$stacks2 = $stacks;
while (($moves = fgets($input)) !== false) {
    $moves = substr($moves, 0, -1);
    if (!$moves) break;
    [$_, $num, $_, $from, $_, $to] = explode(' ', $moves);
    // p1
    for ($n = 0; $n < $num; $n++) {
        $crate = $stacks[$from][0];
        $stacks[$from] = substr($stacks[$from], 1);
        $stacks[$to] = $crate . $stacks[$to];
    }
    // p2
    $crates = substr($stacks2[$from], 0, $num);
    $stacks2[$from] = substr($stacks2[$from], $num);
    $stacks2[$to] = $crates . $stacks2[$to];

}

$p1 = '';
foreach ($stacks as $crates) {
    $p1 .= $crates[0];
}

$p2 = '';
foreach ($stacks2 as $crates) {
    $p2 .= $crates[0];
}

echo "p1: {$p1}\np2: {$p2}\n";
