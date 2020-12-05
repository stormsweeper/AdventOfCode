<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);
$total_tsps = 100;

$ingredients = [];

foreach ($inputs as $line) {
    preg_match('#(?P<ingredient>\w+): capacity (?P<capacity>-?\d+), durability (?P<durability>-?\d+), flavor (?P<flavor>-?\d+), texture (?P<texture>-?\d+), calories (?P<calories>-?\d+)#', $line, $m);
    $ingredients[] = $m;
}

$total_ingredients = count($ingredients);

function try_recipes(array $mix = [], &$best_recipe = [], &$best_score = 0, ?int $cal_req = null): void {
    global $ingredients, $total_tsps, $total_ingredients;
    $current_ingredient = count($mix);
    $current_tsps = array_sum($mix);
    $remaining_tsps = $total_tsps - $current_tsps;
    $to_add = 0;

    // if last ingredient
    if ($current_ingredient === ($total_ingredients - 1)) {
        // fill balance
        $mix[$current_ingredient] = $remaining_tsps;
    }
    // elseif 0 remaining, finish recipe with 0s
    elseif ($remaining_tsps < 1) {
        $mix = array_pad($mix, $total_ingredients, 0);
    }


    // if incomplete
    if (count($mix) < $total_ingredients) {
        
        // loop 0..$remaining_tsps
        $to_add = 0;
        while ($to_add++ <= $remaining_tsps) {
            // add n of ingredient
            $mix[$current_ingredient] = $to_add;
            // recurse to leftover ingredients
            try_recipes($mix, $best_recipe, $best_score, $cal_req);
        }
        return;
    }
    // else validate recipe
    $score = score_recipe($mix, $cal_req);
    if ($score > $best_score) {
        $best_score = $score;
        $best_recipe = $mix;
    }
}

function score_recipe(array $recipe, ?int $cal_req = null): int {
    global $ingredients;
    $score = [
        'capacity' => 0,
        'durability' => 0,
        'flavor' => 0,
        'texture' => 0,
    ];
    if (isset($cal_req)) {
        $cals = 0;
        foreach ($recipe as $ing => $amt) {
             $cals += $amt * $ingredients[$ing]['calories'];
        }
        if ($cals !== $cal_req) {
            return 0;
        }
    }
    foreach (array_keys($score) as $prop) {
        foreach ($recipe as $ing => $amt) {
             $score[$prop] += $amt * $ingredients[$ing][$prop];
        }
        if ($score[$prop] < 1) {
            return 0;
        }
    }
    return array_product($score);
}

try_recipes([], $best_recipe, $best_score);
try_recipes([], $best_500_recipe, $best_500_score, 500);

echo  "Best possible cookie has a score of: {$best_score}\n";
echo  "Best possible 500kcal cookie has a score of: {$best_500_score}\n";




