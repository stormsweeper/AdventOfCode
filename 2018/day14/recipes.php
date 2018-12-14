<?php

$iterations = intval($argv[1] ?? 5);

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

while(strlen($scores) < $iterations + 10) {
    // append new scores
    $scores .= getNextScores($scores, $elfs);
    //echo "{$scores}\n";
    // rotate elfs
    $elfs = rotateElfs($scores, $elfs);
    //echo json_encode($elfs) . "\n";
}

$final = substr($scores, $iterations, 10);

echo "FINAL {$final}\n";

