<?php

$input = $argv[1];
$iterations = intval($argv[2] ?? 1);

function looksay($in) {
    $in .= 'z'; // sentinel
    $out = '';
    $ccount = 0; $last = $in[0];
    for ($i = 0; $i < strlen($in); $i++) {
        $curr = $in[$i];
        if ($curr === $last) {
            $ccount++;
            continue;
        }
        $out .= $ccount . $last;
        $last = $curr;
        $ccount = 1;
    }
    return $out;
}

$result = $input;

for ($i = 0; $i < $iterations; $i++) {
    $result = looksay($result);
}

echo strlen($result);