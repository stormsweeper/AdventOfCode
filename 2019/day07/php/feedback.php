<?php

require_once 'intputerv3.php';

$input = trim(file_get_contents($argv[1]));
$input = explode(",", $input);
$program = array_map('intval', $input);

// Max thruster signal 139629729 (from phase setting sequence 9,8,7,6,5):
//$program = [3,26,1001,26,-4,26,3,27,1002,27,2,27,1,27,26,27,4,27,1001,28,-1,28,1005,28,6,99,0,0,5];

// Max thruster signal 18216 (from phase setting sequence 9,7,8,5,6):
//$program = [3,52,1001,52,-5,52,3,53,1,52,56,54,1007,54,5,55,1005,55,26,1001,54,
//-5,54,1105,1,12,1,53,54,53,1008,54,0,55,1001,55,1,55,2,53,55,53,4,
//53,1001,56,-1,56,1005,56,6,99,0,0,0,0,10];

$permutations = [];
$phases = range(5, 9);
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
    $ampE->chainPuter($ampA);

    while (!$ampE->hasHalted()) {
        $ampA->run();
        $ampB->run();
        $ampC->run();
        $ampD->run();
        $ampE->run();
    }

    $output = $ampE->lastOutput();

    if ($output > $best_output) {
        $best_output = $output;
        $best_phases = $phases;
    }
}

echo $best_output;
