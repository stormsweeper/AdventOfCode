<?php

$input = array_filter(array_map('trim', file($argv[1])));

$wires = [];

function wireItUp() {
    global $wires, $input;
    $max = 10000;
    while (!isset($wires['a']) && $max--) {
        foreach ($input as $desc) {
            [$op, $out] = explode(' -> ', $desc);
            if (isset($wires[$out])) {
                continue;
            }
            $op = explode(' ', $op);
            // all "sets" have just the 1 operand
            if (count($op) === 1) {
                $v = readWire($op[0]);
                if (isset($v)) {
                    setWire($out, $v);
                }
                continue;
            }
    
            // NOT has 2
            if (count($op) === 2) {
                $v = readWire($op[1]);
                if (isset($v)) {
                    setWire($out, ~ $v);
                }
                continue;
            }
    
            // everything else
            $a = readWire($op[0]);
            $b = readWire($op[2]);
            if (!isset($a) || !isset($b)) {
                continue;
            }
            switch ($op[1]) {
                case 'AND':
                    setWire($out, $a & $b);
                    break;
                case 'OR':
                    setWire($out, $a | $b);
                    break;
                case 'LSHIFT':
                    setWire($out, $a << $b);
                    break;
                case 'RSHIFT':
                    setWire($out, $a >> $b);
                    break;
            }
        }
    }
}

function readWire($w) {
    global $wires;
    if (is_numeric($w)) {
        return intval($w);
    }
    return $wires[$w] ?? null;
}

function setWire($w, $val) {
    global $wires;
    return $wires[$w] = $val & (pow(2,16) - 1);
}

wireItUp();
echo "first pass a: {$wires['a']}\n";
$wires = [ 'b' => $wires['a'] ];

wireItUp();
echo "second pass a: {$wires['a']}\n";


