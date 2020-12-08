<?php

$inputs = trim(file_get_contents($argv[1]));

preg_match_all('#([a-z]{3}) ([+-]\d+)#', $inputs, $m, PREG_SET_ORDER);

$program = array_map(
    function($inst) {
        return [
            $inst[1], // opcode
            intval($inst[2]), // amt
        ];
    },
    $m
);



$ptr = 0;
$executed = [];
$acc = 0;
$eof = count($program);

function advance(?int $hax = null): bool {
    global $ptr, $executed, $acc, $program, $eof;
    if (isset($executed[$ptr])) {
        #echo "Ended at infinite loop\n";
        return false;
    }
    if ($ptr === $eof) {
        #echo "Ended at EOF\n";
        return false;
    }
    if ($ptr < 0) {
        throw new RuntimeException("bad ptr: {$ptr}");
    }
    $executed[$ptr] = true;
    list($op, $amt) = $program[$ptr];
    if (($hax ?? -1) === $ptr) {
        if ($op === 'nop') {
            $op = 'jmp';
            #echo "Patching NOP to JMP at {$ptr}\n";
        }
        elseif ($op === 'jmp') {
            $op = 'nop';
            #echo "Patching JMP to NOP at {$ptr}\n";
        }
    }
    if ($op === 'acc') {
        $acc += $amt;
        $ptr++;
        return true;
    }
    if ($op === 'jmp') {
        $ptr += $amt;
        return true;
    }
    if ($op === 'nop') {
        $ptr ++;
        return true;
    }
    throw new RuntimeException("unsupported op: {$op}");
}

while (advance()) {
    // nada
}

echo "Value of ACC at infinite loop: {$acc}\n";

// part 2

$hax = 0;
do {
    $acc = 0;
    $ptr = 0;
    $executed = [];
    while (advance($hax)) {
        // nada
    }
    $hax++;
} while ($ptr != $eof);

echo "Value of ACC at program end: {$acc}\n";
