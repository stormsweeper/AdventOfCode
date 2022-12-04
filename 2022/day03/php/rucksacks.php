<?php

$priorities = array_merge(['.'], range('a', 'z'), range('A', 'Z'));
$priorities = array_flip($priorities);

$sacks = fopen($argv[1], 'r');

function find_common(string $sack): string {
    $len = strlen($sack) / 2;
    $a = array_keys(count_chars(substr($sack, 0, $len), 1));
    $b = array_keys(count_chars(substr($sack, $len), 1));
    $dupe = array_intersect($a, $b);
    return chr(array_pop($dupe));
}

function find_badge(array $sacks): string {
    $a = array_keys(count_chars($sacks[0], 1));
    $b = array_keys(count_chars($sacks[1], 1));
    $c = array_keys(count_chars($sacks[2], 1));
    $dupe = array_intersect($a, $b, $c);
    return chr(array_pop($dupe));
}

$group = [];
$p1 = $p2 = 0;
while (($sack = fgets($sacks)) !== false) {
    $sack = trim($sack);
    if (!$sack) continue;
    $p1 += $priorities[ find_common($sack) ];
    $group[] = $sack;
    if (count($group) === 3) {
        $p2 += $priorities[ find_badge($group) ];
        $group = [];
    }
}

echo "p1: {$p1} p2: {$p2}\n";