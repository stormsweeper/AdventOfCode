<?php

$inputs = trim(file_get_contents($argv[1]));
list($polymer, $inputs) = explode("\n\n", $inputs);

$max_steps = intval($argv[2]??10);

$reactions = [];
preg_match_all('/([A-Z])([A-Z]) -> ([A-Z])/', $inputs, $match_sets, PREG_SET_ORDER);
foreach ($match_sets as list(, $a, $b, $c)) {
    $reactions["{$a}{$b}"] = ["{$a}{$c}", "{$c}{$b}"];
}


$rcounts = [];
$max = strlen($polymer);
$first_el = $polymer[0];
$last_el = substr($polymer, -1);
for ($i = 0; $i < $max - 1; $i++) {
    $reagents = substr($polymer, $i, 2);
    $rcounts[$reagents] = ($rcounts[$reagents]??0) + 1;
}

for ($step = 1; $step <= $max_steps; $step++) {
    $next = [];
    foreach ($rcounts as $r => $count) {
        $k = $reactions[$r];
        $next[$k[0]] = ($next[$k[0]]??0) + $count;
        $next[$k[1]] = ($next[$k[1]]??0) + $count;
    }
    $rcounts = $next;
}

$ecounts = [$first_el => 1, $last_el => 1];

foreach ($rcounts as $r => $count) {
    $ecounts[$r[0]] = ($ecounts[$r[0]]??0) + $count;
    $ecounts[$r[1]] = ($ecounts[$r[1]]??0) + $count;
}

echo (max($ecounts) - min($ecounts))/2;
