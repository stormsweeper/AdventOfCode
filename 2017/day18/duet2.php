<?php

require_once __DIR__ . '/progclass.php';

$assembly = explode("\n", file_get_contents($argv[1]));
$progA = new Prog(0, $assembly);
$progB = new Prog(1, $assembly);
$progA->setPartner($progB);

$clock = 0;
while (!$progA->isTerminated() && !$progB->isTerminated()) {
    $clock++;
    $progA->nextInstruction();
    $progB->nextInstruction();
}

print_r($progB->numSent());