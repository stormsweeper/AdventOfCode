<?php

$program = trim(file_get_contents($argv[1]));
$program = explode("\n", $program);


function apply_mask(string $mask, int $val): int {
    for ($i = 0; $i < 36; $i++) {
        if ($mask[$i] === 'X') continue;
        $shift = 36 - $i - 1;
        $adj = (1 << $shift);
        if ($mask[$i] === '1') {
            $val |= $adj;
        } else {
            $val &= ~$adj;
        }
    }
    return $val;
}

$mask = str_repeat('X', 36);
$mem = [];

foreach ($program as $inst) {
    if (strpos($inst, 'mask') === 0) {
        $mask = substr($inst, 7);
        continue;
    }
    preg_match('#\[(\d+)\] = (\d+)#', $inst, $m);
    $addr = $m[1];
    $val = intval($m[2]);
    $val = apply_mask($mask, $val);
    $mem[$addr] = $val;
}

echo array_sum($mem);