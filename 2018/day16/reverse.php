<?php

require_once __DIR__ . '/opcodes.php';

$input = file_get_contents($argv[1]);

$regex = '/Before: \[(?<before>[\d,\s]+)\]\n(?<command>[\d\s]+)\nAfter:  \[(?<after>[\d,\s]+)\]/s';

preg_match_all($regex, $input, $tests, PREG_SET_ORDER);

$indeterminate = 0;

$opcode_possibilities = [];

foreach ($tests as $test) {
    $possibles_for_sample = 0;
    $before = array_map('intval', array_map('trim', explode(',', $test['before'])));
    $after  = array_map('intval', array_map('trim', explode(',', $test['after'])));
    [$opcode, $argA, $argB, $argC] = array_map('intval', explode(' ', $test['command']));

    $possible = possibilities($before, $opcode, $argA, $argB, $argC, $after);
    if (count($possible) >= 3) {
        $indeterminate++;
    }
    $opcode_possibilities[$opcode] = array_unique(array_merge(($opcode_possibilities[$opcode] ?? []), $possible));
    //
}

//print_r($opcode_possibilities);

echo "samples w/ >= 3 possible opcodes: {$indeterminate}\n";

$opcodes = [];

while (count($opcode_possibilities)) {
    $definite = array_filter(
        $opcode_possibilities,
        function($poss) {
            return count($poss) === 1;
        }
    );
    $definite = array_map('array_pop', $definite);
    // flipping as merging number indices causes them to be treated like a list, not a map
    $opcodes = array_merge($opcodes, array_flip($definite));
    $opcode_possibilities = array_diff_key($opcode_possibilities, $definite);
    $opcode_possibilities = array_map(
        function($poss) use ($definite) {
            return array_diff($poss, $definite);
        },
        $opcode_possibilities
    );
}

$opcodes = array_flip($opcodes);

[, $program] = explode("\n\n\n\n", $input);
$program = explode("\n", trim($program));

$registers = [0,0,0,0];
//echo 'REG: ' . json_encode($registers) . "\n";
foreach ($program as $line) {
    [$opcode, $argA, $argB, $argC] = array_map('intval', explode(' ', $line));
    $registers = performOp($registers, $opcodes[$opcode], $argA, $argB, $argC);
    //echo "{$opcodes[$opcode]} {$argA} {$argB} {$argC}\n";
    //echo 'REG: ' . json_encode($registers) . "\n";
}

print_r($registers);