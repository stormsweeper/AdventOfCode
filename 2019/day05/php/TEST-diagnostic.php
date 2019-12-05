<?php

require_once 'intputerv2.php';

$input = trim(file_get_contents($argv[1]));
$input = explode(",", $input);
$program = array_map('intval', $input);

// outputs whatever it gets as input
// end state: [<input>,0,4,0,99]
//$program = [3,0,4,0,99];

// multiplies but outputs nothing
// end state: [1002,4,3,4,99]
//$program = [1002,4,3,4,33];


// - Using position mode, consider whether the input is equal to 8; output 1 (if it is) or 0 (if it is not).
//$program = [3,9,8,9,10,9,4,9,99,-1,8];
// - Using position mode, consider whether the input is less than 8; output 1 (if it is) or 0 (if it is not).
//$program = [3,9,7,9,10,9,4,9,99,-1,8];
// - Using immediate mode, consider whether the input is equal to 8; output 1 (if it is) or 0 (if it is not).
//$program = [3,3,1108,-1,8,3,4,3,99];
// - Using immediate mode, consider whether the input is less than 8; output 1 (if it is) or 0 (if it is not).
//$program = [3,3,1107,-1,8,3,4,3,99];


$puter = new IntPuterV2();

$puter->loadProgram($program);
$puter->run();

echo "\n";
echo json_encode($puter->dumpMemory());
