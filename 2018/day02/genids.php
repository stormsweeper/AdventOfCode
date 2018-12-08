<?php

$len = 26;
$max = 250;

$strings = [];

function randomString($len) {
    $out = '';
    while ($len--) {
        $out .= chr( rand(97,122) );
    }
    return $out;
}

$common = randomString($len - 1);
$strings[] = 'a' . $common;
$strings[] = 'z' . $common;

while (count($strings) < $max) {
    $next = randomString($len);
    foreach ($strings as $string) {
        if (levenshtein($string, $next) < 2) {
            continue 2;
        }
    }
    $strings[] = $next;
}

$strings = array_merge(
    [$strings[0]],
    array_slice($strings, 2),
    [$strings[1]]
);

echo implode("\n", $strings);
