<?php

$target = 2020;
$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

$valid = array_filter(
    $inputs,
    function ($line) {
        list($positions, $req, $passwd) = explode(' ', $line, 3);
        $req = $req[0];
        list($pos1, $pos2) = explode('-', $positions);
        $pcounts = count_chars($passwd);
        $found = $pcounts[ord($req)];
        return (($passwd[$pos1 - 1] === $req) + ($passwd[$pos2 - 1] === $req)) === 1;
    }
);

echo count($valid);