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

$puter = new IntPuterV2();

$puter->loadProgram($program);
$puter->run();

echo "\n";
echo json_encode($puter->dumpMemory());
