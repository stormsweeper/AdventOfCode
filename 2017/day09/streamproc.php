<?php

$input = str_split(file_get_contents('input.txt') ?? '');

$depth = 0;
$score = 0;
$garbage = 0;
$in_garbage = false;
$max = count($input);

for ($i = 0; $i < $max; $i++) {
    if (!$in_garbage) {
        if ($input[$i] === '{') {
            $depth++;
        } elseif ($input[$i] === '}') {
            $score += $depth;
            $depth--;
        } elseif ($input[$i] === '<') {
            $in_garbage = true;
        }
    } else {
        if ($input[$i] === '!') {
            $i++;
        } elseif ($input[$i] === '>') {
            $in_garbage = false;
        } else {
            $garbage++;
        }
    }
}

print_r([$score, $garbage]);