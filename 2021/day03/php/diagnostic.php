<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);
$word_size = strlen($inputs[0]);

function int2word(int $d): string {
    global $word_size;
    return str_pad(decbin($d), $word_size, '0', STR_PAD_LEFT);
}

function word2int(string $w): int {
    return intval(bindec($w));
}

// using this vs just ~ to not flip leading 0s
function invert_int(int $d): int {
    global $word_size;
    $max = word2int(str_repeat('1', $word_size));
    return (~$d) & $max;
}

function mc_bits(array $words): int {
    global $word_size;
    $sums = [];
    foreach ($words as $word) {
        for ($bit = 0; $bit < $word_size; $bit++) {
            $sums[$bit] = ($sums[$bit] ?? 0) + $word[$bit];
        }
    }
    
    $half = count($words)/2;
    $mc = '';
    foreach ($sums as $bit => $sum) {
        if ($sum >= $half) {
            $mc[$bit] = 1;
        }
        else {
            $mc[$bit] = 0;
        }
    }

    return word2int($mc);
}

$gamma = mc_bits($inputs);
$epsilon = invert_int($gamma);

$consumption = $gamma * $epsilon;

echo "gamma: {$gamma} epsilon: {$epsilon} consumption: {$consumption}\n";

// part 2

$oxy = $co2 = $inputs;

for ($bit = 0; $bit < $word_size; $bit++) {
    if (count($oxy) > 1) {
        $mc_o = int2word(mc_bits($oxy));
        $oxy = array_filter($oxy, function($w) use ($bit, $mc_o) {
            return $w[$bit] === $mc_o[$bit];
        });
    }

    if (count($co2) > 1) {
        $mc_c = int2word(mc_bits($co2));
        $co2 = array_filter($co2, function($w) use ($bit, $mc_c) {
            return $w[$bit] !== $mc_c[$bit];
        });
    }
}

$oxy = word2int(array_pop($oxy));
$co2 = word2int(array_pop($co2));
$support = $oxy * $co2;

echo "oxy: {$oxy} co2: {$co2} support: {$support}\n";
