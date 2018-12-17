<?php

function performOp($input, $opname, $argA, $argB, $argC) {
    $output = $input;
    switch ($opname) {
        case 'addr':
            $output[$argC] = $input[$argA] + $input[$argB];
            break;

        case 'mulr':
            $output[$argC] = $input[$argA] * $input[$argB];
            break;

        case 'banr':
            $output[$argC] = $input[$argA] & $input[$argB];
            break;

        case 'borr':
            $output[$argC] = $input[$argA] | $input[$argB];
            break;

        case 'gtrr':
            $output[$argC] = $input[$argA] > $input[$argB] ? 1 : 0;
            break;

        case 'eqrr':
            $output[$argC] = $input[$argA] === $input[$argB] ? 1 : 0;
            break;

        case 'addi':
            $output[$argC] = $input[$argA] + $argB;
            break;

        case 'muli':
            $output[$argC] = $input[$argA] * $argB;
            break;

        case 'bani':
            $output[$argC] = $input[$argA] & $argB;
            break;

        case 'bori':
            $output[$argC] = $input[$argA] | $argB;
            break;

        case 'gtri':
            $output[$argC] = $input[$argA] > $argB ? 1 : 0;
            break;

        case 'eqri':
            $output[$argC] = $input[$argA] === $argB ? 1 : 0;
            break;

        case 'setr':
            $output[$argC] = $input[$argA];
            break;

        case 'gtir':
            $output[$argC] = $argA > $input[$argB] ? 1 : 0;
            break;

        case 'eqir':
            $output[$argC] = $argA === $input[$argB] ? 1 : 0;
            break;

        case 'seti':
            $output[$argC] = $argA;
            break;
    }
    return $output;
}

function possibilities($input, $opcode, $argA, $argB, $argC, $expected) {
    $poss = [];

    $try = ['seti'];
    // the register-only ops go here
    if ($argA < 4 && $argB < 4) {
        $try = array_merge($try, ['addr', 'mulr', 'banr', 'borr', 'gtrr', 'eqrr']);
    }

    // the ops that use a register for the first arg
    if ($argA < 4) {
        $try = array_merge($try, ['addi', 'muli', 'bani', 'bori', 'gtri', 'eqri', 'setr']);
    }

    // the ops that use a register for the second arg
    if ($argB < 4) {
        $try = array_merge($try, ['gtir', 'eqir']);
    }

    foreach ($try as $opname) {
        $actual = performOp($input, $opname, $argA, $argB, $argC);
        if ($expected === $actual) {
            $poss[] = $opname;
        }
    }

    return $poss;
}