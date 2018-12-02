<?php

function readValue() {
    global $tape, $cursor;
    return $tape[$cursor] ?? 0;
}

function writeValue($value) {
    global $tape, $cursor;
    return $tape[$cursor] = $value;
}

function nextStep() {
    global $blueprint, $tape, $cursor, $current_state;
    $current_value = readValue();
    list ($nextval, $step, $nextstate) = $blueprint[$current_state][$current_value];
    writeValue($nextval);
    $cursor += $step;
    $current_state = $nextstate;
}

function diagChecksum() {
    global $tape;
    return array_sum($tape);
}

$blueprint = [];
$current_state = null;
$check_after = 0;
$tape = [];
$cursor = 0;

$input = explode("\n\n", file_get_contents($argv[1]));
preg_match('/state ([A-Z]).+after (\d+) steps/s', array_shift($input), $matches);
$current_state = $matches[1];
$check_after = intval($matches[2]);


foreach ($input as $statedesc) {
    $regex = '/In state ([A-Z]).+the value ([0,1]).+to the (left|right).*with state ([A-Z]).+the value ([0,1]).+to the (left|right).*with state ([A-Z])/s';
    preg_match($regex, $statedesc, $matches);
    $fromstate  = $matches[1];
    $value0     = $matches[2];
    $step0      = $matches[3] === 'left' ? -1 : 1;
    $next0      = $matches[4];
    $value1     = $matches[5];
    $step1      = $matches[6] === 'left' ? -1 : 1;
    $next1      = $matches[7];
    $blueprint[$fromstate][0] = [$value0, $step0, $next0];
    $blueprint[$fromstate][1] = [$value1, $step1, $next1];
}

while ($check_after--) {
    nextStep();
}

echo diagChecksum();
