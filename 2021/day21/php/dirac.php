<?php

$input = trim(file_get_contents($argv[1]));

preg_match('/1 starting position: (\d).+2 starting position: (\d)/s', $input, $m);
[, $p1_initial_pos, $p2_initial_pos] = $m;
$p1_pos = $p1_initial_pos;
$p2_pos = $p2_initial_pos;

$p1_score = $p2_score = 0;

function deterministic_die(): int {
    static $result = 1;
    if ($result > 100) $result -= 100;
    return $result++;
}

function take_turn(int $start): int {
    $end = $start + deterministic_die() + deterministic_die() + deterministic_die();
    while ($end > 10) $end -= 10;
    return $end;
}

$rolls = 0;
while ($p1_score < 1000 && $p2_score < 1000) {
    $p1_score += $p1_pos = take_turn($p1_pos);
    $rolls += 3;
    if ($p1_score >= 1000) break;
    $p2_score += $p2_pos = take_turn($p2_pos);
    $rolls += 3;
}

$part1 = min($p1_score, $p2_score) * $rolls;

echo "p1 score:{$p1_score} pos:{$p1_pos}\n";
echo "p2 score:{$p2_score} pos:{$p2_pos}\n";
echo "rolls:{$rolls} result:{$part1}\n";

// part 2
// each roll of 3d3 has 27 possible results, but only 7 uniques (min 3, max 9, mode/median 6)
// this wil be a map of result => count
$dirac_dice_results = array_fill(3, 7, 0);
for ($a = 3; $a >= 1; $a--) {
    for ($b = 3; $b >= 1; $b--) {
        for ($c = 3; $c >= 1; $c--) $dirac_dice_results[$a + $b + $c]++;
    }
}

$p1_wins = $p2_wins = 0;
function take_dirac_turn(
        int $pA_starting_pos, int $pA_starting_score, int &$pA_wins, 
        int $pB_pos, int $pB_score, int &$pB_wins, 
        int $prior_universes = 1
    ): void {
    global $dirac_dice_results;

    //echo "prior:{$prior_universes}\npA:{$pA_starting_pos}/{$pA_starting_score}/{$pA_wins}\npB:{$pB_pos}/{$pB_score}/{$pB_wins}\n\n";
    foreach ($dirac_dice_results as $result => $count) {
        // see if pA wins
        $pA_pos = $pA_starting_pos + $result; 
        if ($pA_pos > 10) $pA_pos -= 10;
        $pA_score = $pA_starting_score + $pA_pos;
        if ($pA_score >= 21) {
            $pA_wins += $count * $prior_universes;
            continue;
        }

        // else take another set of turns with the other player
        take_dirac_turn($pB_pos, $pB_score, $pB_wins, $pA_pos, $pA_score, $pA_wins, $count * $prior_universes);
    }
}

take_dirac_turn($p1_initial_pos, 0, $p1_wins, $p2_initial_pos, 0, $p2_wins);

echo "p1 wins:{$p1_wins}\n";
echo "p2 wins:{$p2_wins}\n";
