<?php

$target = 2020;
$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

$valid = array_filter(
    $inputs,
    function ($line) {
        list($range, $req, $passwd) = explode(' ', $line, 3);
        $req = $req[0];
        list($min, $max) = explode('-', $range);
        $pcounts = count_chars($passwd);
        $found = $pcounts[ord($req)];
        return $found >= $min && $found <= $max;
    }
);

echo count($valid);