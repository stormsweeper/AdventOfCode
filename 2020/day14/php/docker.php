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

$p1 = array_sum($mem);
echo "Part 1: {$p1}\n";

$mask = str_repeat('0', 36);
$mem = [];

function addrs(string $mask, int $addr): array {
    $addrs = [''];
    $addr = str_pad(decbin($addr), 36, '0', STR_PAD_LEFT);
    for ($i = 0; $i < 36; $i++) {
        if ($mask[$i] === '0') {
            $bits = [$addr[$i]];
        } elseif ($mask[$i] === '1') {
            $bits = [1];
        } else {
            $bits = [0,1];
        }
        $next = [];
        foreach ($addrs as $a) {
            foreach ($bits as $b) {
                $next[] = $a.$b;
            }
        }
        $addrs = $next;
    }
    return $addrs;
}

foreach ($program as $inst) {
    if (strpos($inst, 'mask') === 0) {
        $mask = substr($inst, 7);
        continue;
    }
    preg_match('#\[(\d+)\] = (\d+)#', $inst, $m);
    $val = intval($m[2]);
    foreach(addrs($mask, $m[1]) as $addr) {
        $mem[$addr] = $val;   
    }
}

$p2 = array_sum($mem);
echo "Part 2: {$p2}\n";
