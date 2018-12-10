<?php

$strings = array_filter(array_map('trim', file($argv[1])));

function meetsCriteria1($string) {
    $len = strlen($string);
    for ($i = 0; $i < $len - 3; $i++) {
        $pair = substr($string, $i, 2);
        $next = strpos($string, $pair, $i + 2);
        if ($next !== false) {
            return true;
        }
    }
    return false;
}

function meetsCriteria2($string) {
    $len = strlen($string);
    for ($i = 0; $i < $len - 2; $i++) {
        if ($string[$i] === $string[$i + 2]) {
            return true;
        }
    }
    return false;
}

$nice = 0;
foreach ($strings as $string) {
    if (meetsCriteria1($string) && meetsCriteria2($string)) {
        $nice++;
    }
}

echo $nice;
