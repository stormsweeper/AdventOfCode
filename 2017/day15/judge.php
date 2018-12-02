<?php

$inputA = intval($argv[1]);
$inputB = intval($argv[2]);

$factorA = 16807;
$factorB = 48271;

function nextval($prev, $factor) {
    return ($prev * $factor) % 2147483647;
}

function isMatch($a, $b) {
    $a = substr(decbin($a), -16);
    $b = substr(decbin($b), -16);
    return $a === $b;
}

$matched = 0;
for ($i = 0; $i < 4e+7; $i++) {
    if ($i % 1e+6 === 0) {
        echo "iteration {$i}\n";
    }
    $inputA = nextval($inputA, $factorA);
    $inputB = nextval($inputB, $factorB);
    $matched += isMatch($inputA, $inputB);
}

echo $matched;