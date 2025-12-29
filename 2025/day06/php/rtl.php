<?php

function toks(string $line): array {
    return preg_split('/\s+/', trim($line), -1, PREG_SPLIT_NO_EMPTY);
}

$sheet = explode("\n", trim(file_get_contents($argv[1])));

// add a space line so you don't end up with `123+` in the rotated version
$ops_row = array_pop($sheet);
$sheet[] = '';
$sheet[] = $ops_row;

$rotated = array_fill(0, strlen($sheet[0]), str_repeat(' ', count($sheet)));

foreach ($sheet as $x => $line) {
    for ($y = 0; $y < strlen($line); $y++) {
        $rotated[$y][$x] = $line[$y];
    }
}


$rotated[] = '';

$p2 = 0;

$sum = 0; $op = '';

foreach ($rotated as $line) {
    $toks = toks($line);
    $tc = count($toks);
    // echo json_encode([$toks, $tc, $sum, $op, $p2]) . "\n";
    if ($tc === 2) {
        $sum = intval($toks[0]);
        $op = $toks[1];
    } elseif ($tc === 1) {
        if ($op === '+') {
            $sum += intval($toks[0]);
        } elseif ($op === '*') {
            $sum *= intval($toks[0]);
        } else {
            throw new Exception('wat');
        }
    } else {
        // echo "summing...\n";
        $p2 += $sum;
        $sum = 0; $op = '';
    }
}

echo "p2: {$p2}\n";
