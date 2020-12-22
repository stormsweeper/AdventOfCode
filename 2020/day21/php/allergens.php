<?php

$input = trim(file_get_contents($argv[1]));
$input = explode("\n", $input);

$all_ingredients = [];
$by_allergen = [];

foreach ($input as $line) {
    list ($ings, $alls) = explode(' (contains ', $line);
    $ings = explode(' ', $ings);
    $alls = explode(', ', substr($alls, 0, -1));

    foreach ($alls as $allergen) {
        if (!isset($by_allergen[$allergen])) {
            $by_allergen[$allergen] = $ings;
        } else {
            $by_allergen[$allergen] = array_values(array_intersect($by_allergen[$allergen], $ings));
        }
    }
    foreach ($ings as $ingredient) {
        $all_ingredients[$ingredient] = ($all_ingredients[$ingredient] ?? 0) + 1;
    }
}

#uasort($by_allergen, function($a, $b) {return count($a) <=> count($b);});

$unsafe = [];
$consider = $by_allergen;
while ($consider) {
    $next = [];
    foreach ($consider as $allergen => $ings) {
        $maybes = array_diff($ings, $unsafe);
        if (count($maybes) === 1) {
            $unsafe[$allergen] = array_pop($maybes);
        } else {
            $next[$allergen] = $maybes;
        }
    }
    $consider = $next;
}

$safe = array_diff_key($all_ingredients, array_flip($unsafe));
$p1 = array_sum($safe);

echo "Part 1: {$p1}\n";

ksort($unsafe);
$p2 = implode(',', $unsafe);
echo "Part 2: {$p2}\n";