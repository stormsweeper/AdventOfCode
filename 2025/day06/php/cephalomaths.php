<?php

function toks(string $line): array {
    return preg_split('/\s+/', trim($line));
}


$sheet = explode("\n", trim(file_get_contents($argv[1])));

$operators = toks(array_pop($sheet));

$sums = array_map('intval', toks(array_pop($sheet)));

foreach ($sheet as $line) {
    foreach (toks($line) as $i => $v) {
        if ($operators[$i] === '+') {
            $sums[$i] += $v;
        } else {
            $sums[$i] *= $v;
        }
    }
}

$p1 = array_sum($sums);
echo "p1: {$p1}\n";
