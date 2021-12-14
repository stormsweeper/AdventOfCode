<?php

$inputs = trim(file_get_contents($argv[1]));
list($polymer, $inputs) = explode("\n\n", $inputs);

$max_steps = intval($argv[2]??10);

$reactions = [];
preg_match_all('/([A-Z])([A-Z]) -> ([A-Z])/', $inputs, $match_sets, PREG_SET_ORDER);
foreach ($match_sets as list(, $a, $b, $c)) {
    $reactions["{$a}{$b}"] = "{$a}{$c}{$b}";
}


for ($step = 1; $step <= $max_steps; $step++) {
    $next = '';
    $chainlen = strlen($polymer);
    for ($i = 0; $i < $chainlen - 1; $i++) {
        $from = substr($polymer, $i, 2);
        $next = substr($next, 0, -1) . $reactions[$from];
    }
    $polymer = $next;
}

// 1 here only keeps chars > 0, vs all ASCII
$min = PHP_INT_MAX; $max = 0;
foreach (count_chars($polymer, 1) as $count) {
    $min = min($min, $count);
    $max = max($max, $count);
}

echo $max - $min;

