<?php

require_once __DIR__ . '/common.php';

BattleMap::initialize(file_get_contents($argv[1]));

$battle = BattleMap::getInstance();

$max_turns = intval($argv[2]);
while ($battle->hasCombatants() && $battle->completedRounds() < $max_turns) {
    //$battle->printDebug();
    $battle->doTurn();
}

echo $battle->currentScore() . "\n";
$battle->printDebug();
// get total HP of winners
// multiply by # of full rounds
