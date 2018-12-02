<?php

$passfile = fopen($argv[1], 'r');
$valid = 0;

while (($line = fgets($passfile)) !== false) {
    $line = substr($line, 0, strlen($line) - 1);
    $split = array_map(
        function($word) {
            $word = str_split($word);
            sort($word);
            return implode('', $word);
        },
        explode(' ', $line)
    );
    if (count($split) === count(array_unique($split))) {
        $valid++;
    }
}

echo $valid;