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

$maybes = [];
foreach ($by_allergen as $ings) {
    foreach ($ings as $ing) $maybes[$ing] = 1;
}

$safe = array_diff_key($all_ingredients, $maybes);
$p1 = array_sum($safe);

echo "Part 1: {$p1}\n";