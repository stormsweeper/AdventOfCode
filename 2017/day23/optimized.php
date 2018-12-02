<?php

// these are from instructions
$b = 108100;
$c = 125100;

// guessed based on jump commands
$step = 17;

$np = 0;

// capped by sqrt(125100), skipping 2 as that's faster to check
$primes = [3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53, 59, 61, 67, 71, 73, 79, 83, 89, 97, 101, 103, 107,
           109, 113, 127, 131, 137, 139, 149, 151, 157, 163, 167, 173, 179, 181, 191, 193, 197, 199, 211, 223, 227, 229,
           233, 239, 241, 251, 257, 263, 269, 271, 277, 281, 283, 293, 307, 311, 313, 317, 331, 337, 347, 349, 353];

$composite = 0;
foreach (range($b, $c, $step) as $num) {
    // div by 2
    if (!($num & 1)) {
        $composite++;
        continue;
    }

    // just guessed it was counting primes/composites
    foreach ($primes as $prime) {
        if (($num % $prime) === 0) {
            $composite++;
            continue 2;
        }
    }
}

echo $composite;

