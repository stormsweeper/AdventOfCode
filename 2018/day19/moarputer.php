<?php

function performOp($input, $cmd) {
    [$opname, $args] = explode(' ', $cmd, 2);
    [$argA, $argB, $argC] = array_map('intval', explode(' ', $args));
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

$program = array_filter(array_map('trim', file($argv[1])));
$bind_reg = intval(substr(array_shift($program), -1));
$num_instrs = count($program);

$registers = [0, 0, 0, 0, 0, 0];

$inst_ptr = (object)['reg' => $bind_reg, 'value' => 0];

while ($inst_ptr->value < $num_instrs) {
    $ptr_value = $inst_ptr->value;
    $registers[ $inst_ptr->reg ] = $inst_ptr->value;
    $output = performOp($registers, $program[ $inst_ptr->value ]);
    $inst_ptr->value = $output[ $inst_ptr->reg ];
    printf(
        "ip=%d [%s] %s [%s]\n",
        $ptr_value,
        implode(', ', $registers),
        $program[$ptr_value],
        implode(', ', $output)
    );
    $inst_ptr->value++;
    $registers = $output;
}

//print_r($inst_ptr);
//print_r($registers);
