<?php

require_once __DIR__ . '/progclass.php';

$assembly = explode("\n", file_get_contents($argv[1]));
$progA = new Prog(0, $assembly);

$clock = 0;
while (!$progA->isTerminated()) {
    $clock++;
    $progA->nextInstruction();
}

print_r($progA->instructionCounts());
