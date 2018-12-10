<?php

$strings = array_filter(array_map('trim', file($argv[1])));

function hasEnoughVowels($string) {
    $vowels = ['a', 'e', 'i', 'o', 'u'];
    $vc = 0;
    foreach ($vowels as $v) {
        $vc += substr_count($string, $v);
        if ($vc >= 3) {
            return true;
        }
    }
    return false;
}

function hasDoubleLetters($string) {
    $letters = range('a', 'z');
    foreach ($letters as $l) {
        if (strpos($string, "{$l}{$l}") !== false) {
            return true;
        }
    }
    return false;
}

function hasNoDisallowed($string) {
    $disallowed = ['ab', 'cd', 'pq', 'xy'];
    foreach ($disallowed as $d) {
        if (strpos($string, $d) !== false) {
            return false;
        }
    }
    return true;
}

$nice = 0;
foreach ($strings as $string) {
    if (hasEnoughVowels($string) && hasDoubleLetters($string) && hasNoDisallowed($string)) {
        $nice++;
    }
}

echo $nice;