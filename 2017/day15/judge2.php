<?php

$inputA = intval($argv[1]);
$inputB = intval($argv[2]);

$factorA = 16807;
$factorB = 48271;

$divisoA = 4;
$divisoB = 8;

function nextval($prev, $factor, $diviso, $depth = 0) {
    if ($depth && ($depth % 100 === 0)) {
        echo "Holy Recursion, Batman! {$depth}\n";
    }
    $next = ($prev * $factor) % 2147483647;
    if ($next % $diviso !== 0) {
        return nextval($next, $factor, $diviso, $depth + 1);
    }
    return $next;
}

function isMatch($a, $b) {
    $a = substr(decbin($a), -16);
    $b = substr(decbin($b), -16);
    return $a === $b;
}

$matched = 0;
for ($i = 0; $i < 5e+6; $i++) {
    if ($i % 1e+6 === 0) {
        echo "iteration {$i}\n";
    }
    $inputA = nextval($inputA, $factorA, $divisoA);
    $inputB = nextval($inputB, $factorB, $divisoB);
    $matched += isMatch($inputA, $inputB);
}

echo $matched;