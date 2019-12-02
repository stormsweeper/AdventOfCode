<?php

$input = trim(file_get_contents($argv[1]));
$input = explode(",", $input);

$override1 = $argv[2] ?? null;
if (isset($override1)) {
    $input[1] = $override1;
}
$override2 = $argv[3] ?? null;
if (isset($override2)) {
    $input[2] = $override2;
}

$registers = array_map('intval', $input);
$cursor = 0;

define('OPCODE_ADD', 1);
define('OPCODE_MULTIPLY', 2);
define('OPCODE_EXIT', 99);

while ($cursor < count($registers)) {
    @list($opcode, $a, $b, $c) = array_slice($registers, $cursor, 4);
    if ($opcode === OPCODE_EXIT) {
        echo "exit\n";
        break;
    } elseif ($opcode === OPCODE_ADD) {
        echo "add reg{$a} ({$registers[$a]}) reg{$b} ({$registers[$b]}) to reg{$c}\n";
        $registers[$c] = $registers[$a] + $registers[$b];
    } elseif ($opcode === OPCODE_MULTIPLY) {
        echo "multi reg{$a} ({$registers[$a]}) reg{$b} ({$registers[$b]}) to reg{$c}\n";
        $registers[$c] = $registers[$a] * $registers[$b];
    } else {
        throw new RuntimeException('Unknown opcode: ' . $opcode);
    }
    $cursor += 4;
}

echo json_encode($registers);

