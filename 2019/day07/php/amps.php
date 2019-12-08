<?php

require_once 'intputerv3.php';

$input = trim(file_get_contents($argv[1]));
$input = explode(",", $input);
$program = array_map('intval', $input);

// Max thruster signal 43210 (from phase setting sequence 4,3,2,1,0):
//$program = [3,15,3,16,1002,16,10,16,1,16,15,15,4,15,99,0,0];

// Max thruster signal 54321 (from phase setting sequence 0,1,2,3,4):
//$program = [3,23,3,24,1002,24,10,24,1002,23,-1,23,101,5,23,23,1,24,23,23,4,23,99,0,0];

// Max thruster signal 65210 (from phase setting sequence 1,0,4,3,2):
//$program = [3,31,3,32,1002,32,10,32,1001,31,-2,31,1007,31,0,33,1002,33,7,33,1,33,31,31,1,32,31,31,4,31,99,0,0,0];

$permutations = [];
$phases = range(0, 4);
while(count($permutations) < 120) {
    if (!in_array($phases, $permutations, true)) {
        $permutations[] = $phases;
    }
    shuffle($phases);
}


$best_output = -100000;
$best_phases = null;
foreach ($permutations as $phases) {
    $ampA = new IntPuterV3;
    $ampA->queueInput($phases[0]);
    $ampA->queueInput(0);
    $ampA->loadProgram($program);

    $ampB = new IntPuterV3;
    $ampB->queueInput($phases[1]);
    $ampB->loadProgram($program);
    $ampA->chainPuter($ampB);

    $ampC = new IntPuterV3;
    $ampC->queueInput($phases[2]);
    $ampC->loadProgram($program);
    $ampB->chainPuter($ampC);

    $ampD = new IntPuterV3;
    $ampD->queueInput($phases[3]);
    $ampD->loadProgram($program);
    $ampC->chainPuter($ampD);

    $ampE = new IntPuterV3;
    $ampE->queueInput($phases[4]);
    $ampE->loadProgram($program);
    $ampD->chainPuter($ampE);

    $ampA->run();
    $ampB->run();
    $ampC->run();
    $ampD->run();
    $ampE->run();

    $output = $ampE->lastOutput();

    if ($output > $best_output) {
        $best_output = $output;
        $best_phases = $phases;
    }
}

echo $best_output;
