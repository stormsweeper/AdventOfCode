<?php

$input = trim(file_get_contents($argv[1]));
$input = explode("\n", $input);

$output = array_map(
    function($line) {
        $mass = intval($line);
        return floor($mass / 3) - 2;
    },
    $input
);
echo array_sum($output);