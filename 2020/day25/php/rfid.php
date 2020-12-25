<?php

$card_key = intval($argv[1]);
$door_key = intval($argv[2]);

function transform(int $value, int $subject): int {
    return ($value * $subject) % 20201227;
}

// determine the loop size of keys
$card_loop = $door_loop = null;
$pk_val = 1;
$loop = 0;
while (!isset($card_loop) || !isset($door_loop)) {
    $loop++;
    $pk_val = transform($pk_val, 7);
    if ($pk_val === $card_key) {
        $card_loop = $loop;
    } elseif ($pk_val === $door_key) {
        $door_loop = $loop;
    }
}

// calculate final key
$ek_val = 1;
for ($i = 0; $i < $door_loop; $i++) {
    $ek_val = transform($ek_val, $card_key);
}

echo $ek_val;