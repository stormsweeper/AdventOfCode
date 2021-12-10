<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

define('VALID_CHUNKS', ['[]','{}','()','<>']);
define('INVALID_SCORES', [')'=>3, ']'=>57, '}'=>1197, '>'=>25137]);

function score_invalid(string $line): int {
    $replaced = 0;
    do {
        $line = str_replace(VALID_CHUNKS, '', $line, $replaced);
    }
    while ($replaced > 0);
    if ($line === '') return 0;
    if (preg_match('/[\[(<{][\])>}]/', $line, $m)) {
        return INVALID_SCORES[$m[0][1]];
    }
    return 0;
}

$scores = array_map('score_invalid', $inputs);
echo array_sum($scores);