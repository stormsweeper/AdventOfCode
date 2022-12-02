<?php

$strats = fopen($argv[1], 'r');

$moves = [];

const ROCK = 'rock';
$moves['A'] = $moves['X'] = ROCK;
const PAPER = 'paper';
$moves['B'] = $moves['Y'] = PAPER;
const SCISSORS = 'scissors';
$moves['C'] = $moves['Z'] = SCISSORS;

$beats = [
    ROCK => PAPER,
    PAPER => SCISSORS,
    SCISSORS => ROCK,
];

$values = [
    ROCK => 1,
    PAPER => 2,
    SCISSORS => 3,
];

function score_strat(string $strat): int {
    global $moves, $beats, $values;
    $opp_move = $moves[ $strat[0] ];
    $my_move = $moves[ $strat[2] ];

    if ($opp_move === $my_move) return $values[$my_move] + 3;
    if ($beats[$opp_move] === $my_move) return $values[$my_move] + 6;
    return $values[$my_move];
}

// part 1
$score = 0;
while (($strat = fgets($strats)) !== false) {
    $strat = trim($strat);
    if (!$strat) continue;
    $score += score_strat($strat);
}

echo $score;
