<?php

require_once 'intputerv4.php';

$input = trim(file_get_contents($argv[1]));
$input = explode(",", $input);
$program = array_map('intval', $input);

$puter = new IntPuterV4();

$puter->loadProgram($program);
$puter->run();

//echo "\n";
//echo json_encode($puter->dumpMemory());
