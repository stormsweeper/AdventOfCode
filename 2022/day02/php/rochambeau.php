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
$loses = array_flip($beats);

$values = [
    ROCK => 1,
    PAPER => 2,
    SCISSORS => 3,
];

function score_strat_p1(string $strat): int {
    global $moves, $beats, $values;
    $opp_move = $moves[ $strat[0] ];
    $my_move = $moves[ $strat[2] ];

    if ($opp_move === $my_move) return $values[$my_move] + 3;
    if ($beats[$opp_move] === $my_move) return $values[$my_move] + 6;
    return $values[$my_move];
}

function score_strat_p2(string $strat): int {
    global $moves, $beats, $loses, $values;
    $opp_move = $moves[ $strat[0] ];
    $need = $strat[2];
    // lose
    if ($need === 'X') {
        $my_move = $loses[ $opp_move ];
        return $values[$my_move];
    }
    // draw
    if ($need === 'Y') {
        $my_move = $opp_move;
        return $values[$my_move] + 3;
    }
    // win
    $my_move = $beats[ $opp_move ];
    return $values[$my_move] + 6;
}

// part 1
$p1_score = $p2_score = 0;
while (($strat = fgets($strats)) !== false) {
    $strat = trim($strat);
    if (!$strat) continue;
    $p1_score += score_strat_p1($strat);
    $p2_score += score_strat_p2($strat);
}

echo "p1: {$p1_score} p2: {$p2_score} \n";
