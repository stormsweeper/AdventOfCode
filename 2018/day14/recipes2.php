<?php

$search = $argv[1];

$elfs = [0, 1];

$scores = '37';

function getNextScores($scores, $elfs) {
    return (string)array_sum(
        array_map(
            function($e) use ($scores) {
                return $scores[$e];
            },
            $elfs
        )
    );
}

function rotateElfs($scores, $elfs) {
    return array_map(
        function($e) use ($scores) {
            return ($e + 1 + $scores[$e]) % strlen($scores);
        },
        $elfs
    );
}

$i = 0;
while(strpos(substr($scores, -10), $search) === false) {
    $i++;
    // append new scores
    $scores .= getNextScores($scores, $elfs);
    // rotate elfs
    $elfs = rotateElfs($scores, $elfs);
    //if ($i % 100000 === 0) {
    //    echo 'scoreboard size: ' . strlen($scores) . ' ending in: ' . substr($scores, -10) . "\n";
    //    echo json_encode($elfs) . "\n";
    //}
}

$final = strpos($scores, $search);

echo "FINAL {$final}\n";

