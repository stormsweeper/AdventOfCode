<?php

$length = 256;
$hasharray = range(0, $length -1);
$sequence = array_map('ord', str_split($argv[1]));
if (empty($sequence[0])) {
    $sequence = [];
}
$sequence = array_merge($sequence, [17, 31, 73, 47, 23]);

$skip = 0;
$pointer = 0;
$runs = 64;

function twist($step) {
    global $hasharray, $length, $pointer;
    $indices = array_map(
        function($index) use ($length) {
            return $index % $length;
        },
        range($pointer, $pointer + $step - 1)
    );

    $values = array_map(
        function($index) use ($hasharray) {
            return $hasharray[$index];
        },
        array_reverse($indices)
    );

    foreach ($indices as $vindex => $hindex) {
        $hasharray[$hindex] = $values[$vindex];
    }
}

while ($runs-- > 0) {
    foreach ($sequence as $step) {
        twist($step);
        $pointer = ($pointer + $step + $skip) % $length;
        $skip++;
    }
}

$compact = [];
for ($i = 0; $i < 16; $i++) {
    $slice = array_slice($hasharray, $i * 16, 16);
    $xored = array_reduce(
        $slice,
        function($carry, $item) {
            return $carry ^ $item;
        },
        0
    );
    $compact[$i] = str_pad(dechex($xored), 2, '0', STR_PAD_LEFT);
}

echo implode('', $compact);