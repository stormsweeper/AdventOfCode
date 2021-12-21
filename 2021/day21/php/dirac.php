<?php

$input = trim(file_get_contents($argv[1]));

preg_match('/1 starting position: (\d).+2 starting position: (\d)/s', $input, $m);
[, $p1_pos, $p2_pos] = $m;

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