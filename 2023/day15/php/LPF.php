<?php

$steps = explode(',', trim(file_get_contents($argv[1])));

function lpf_hash(string $s): int {
    $hash = 0;
    for ($i = 0; $i < strlen($s); $i++) {
        $hash += ord($s[$i]);
        $hash *= 17;
        $hash %= 256;
    }
    return $hash;
}

assert(lpf_hash('HASH') === 52);

$sum = 0;
$boxes = array_fill(0, 256, []);
foreach ($steps as $s) {
    $sum += lpf_hash($s);
    if (strpos($s, '-') !== false) {
        $label = substr($s, 0, -1);
        unset($boxes[lpf_hash($label)][$label]);
    } else {
        [$label, $focal_len] = explode('=', $s);
        $boxes[lpf_hash($label)][$label] = intval($focal_len);
    }
}

echo "p1:{$sum}\n";

$power = 0;

foreach ($boxes as $box_num => $lenses) {
    foreach (array_values($lenses) as $lens_pos => $focal_len) {
        $power += ($box_num + 1) * ($lens_pos + 1) * $focal_len;
    }
}

echo "p2: {$power}\n";