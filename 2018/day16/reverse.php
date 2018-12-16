<?php

function possibilities($input, $opcode, $argA, $argB, $argC, $expected) {
    $poss = [];

    // the register-only ops go here
    if ($argA < 4 && $argB < 4) {
        // addr
        $actual = $input;
        $actual[$argC] = $input[$argA] + $input[$argB];
        if ($expected === $actual) {
            $poss[] = 'addr';
        }

        // mulr
        $actual = $input;
        $actual[$argC] = $input[$argA] * $input[$argB];
        if ($expected === $actual) {
            $poss[] = 'mulr';
        }

        // banr
        $actual = $input;
        $actual[$argC] = $input[$argA] & $input[$argB];
        if ($expected === $actual) {
            $poss[] = 'banr';
        }

        // borr
        $actual = $input;
        $actual[$argC] = $input[$argA] | $input[$argB];
        if ($expected === $actual) {
            $poss[] = 'borr';
        }

        // gtrr
        $actual = $input;
        $actual[$argC] = $input[$argA] > $input[$argB] ? 1 : 0;
        if ($expected === $actual) {
            $poss[] = 'gtrr';
        }

        // eqrr
        $actual = $input;
        $actual[$argC] = $input[$argA] === $input[$argB] ? 1 : 0;
        if ($expected === $actual) {
            $poss[] = 'eqrr';
        }
    }

    // the ops that use a register for the first arg
    if ($argA < 4) {
        // addi
        $actual = $input;
        $actual[$argC] = $input[$argA] + $argB;
        if ($expected === $actual) {
            $poss[] = 'addi';
        }

        // muli
        $actual = $input;
        $actual[$argC] = $input[$argA] * $argB;
        if ($expected === $actual) {
            $poss[] = 'muli';
        }

        // bani
        $actual = $input;
        $actual[$argC] = $input[$argA] & $argB;
        if ($expected === $actual) {
            $poss[] = 'bani';
        }

        // bori
        $actual = $input;
        $actual[$argC] = $input[$argA] | $argB;
        if ($expected === $actual) {
            $poss[] = 'bori';
        }

        // gtri
        $actual = $input;
        $actual[$argC] = $input[$argA] > $argB ? 1 : 0;
        if ($expected === $actual) {
            $poss[] = 'gtri';
        }

        // eqri
        $actual = $input;
        $actual[$argC] = $input[$argA] === $argB ? 1 : 0;
        if ($expected === $actual) {
            $poss[] = 'eqri';
        }

        // setr
        $actual = $input;
        $actual[$argC] = $input[$argA];
        if ($expected === $actual) {
            $poss[] = 'setr';
        }
    }

    // the ops that use a register for the second arg
    if ($argB < 4) {
        // gtir
        $actual = $input;
        $actual[$argC] = $argA > $input[$argB] ? 1 : 0;
        if ($expected === $actual) {
            $poss[] = 'gtir';
        }

        // eqir
        $actual = $input;
        $actual[$argC] = $argA === $input[$argB] ? 1 : 0;
        if ($expected === $actual) {
            $poss[] = 'eqir';
        }
    }

    // this one is always possible
        // seti
        $actual = $input;
        $actual[$argC] = $argA;
        if ($expected === $actual) {
            $poss[] = 'seti';
        }

    return $poss;
}

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

echo $indeterminate;