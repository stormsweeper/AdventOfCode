<?php

$input = fopen($argv[1], 'r');

$registers = ['_max' => 0];

function readRegister($name) {
    global $registers;
    return $registers[$name] ?? 0;
}

function incRegister($name, $val) {
    global $registers;
    return $registers[$name] = readRegister($name) + $val;
}

function evalCondition($condition) {
    $cond = explode(' ', $condition);
    $lh = readRegister($cond[0]);
    $rh = intval($cond[2]);
    switch ($cond[1]) {
         case '<': return $lh < $rh;
         case '<=': return $lh <= $rh;
         case '>': return $lh > $rh;
         case '>=': return $lh >= $rh;
         case '==': return $lh === $rh;
         case '!=': return $lh !== $rh;
    }
    return false;
}

while (($line = fgets($input)) !== false) {
    // b inc 5 if a > 1
    // ['b', 'inc', '5', 'if', 'a > 1']
    $parts = explode(' ', $line, 5);
    if (evalCondition($parts[4])) {
        $val = intval($parts[2]);
        if ($parts[1] === 'dec') {
            $val = 0 - $val;
        }
        $updated = incRegister($parts[0], $val);
        $registers['_max'] = max($registers['_max'], $updated);
    }
}
arsort($registers);
print_r($registers);
