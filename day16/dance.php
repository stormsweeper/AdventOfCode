<?php

$dancers = $starting = 'abcdefghijklmnop';

$steps = str_replace("\n", '', file_get_contents('steps.txt'));
$steps = explode(',', $steps);

$iterations = intval( $argv[1] ?? 1 );

function dance() {
    global $dancers, $starting, $steps;

    foreach ($steps as $step) {
        $cmd = substr($step, 0, 1);
        $args = explode('/', substr($step, 1));
        if ($cmd === 's') {
            // spin
            $split = strlen($dancers) - $args[0];
            $tail = substr($dancers, $split);
            $head = substr($dancers, 0, $split);
            $dancers = $tail . $head;
        } elseif ($cmd === 'x') {
            // exchange
            $a = $dancers[$args[0]];
            $b = $dancers[$args[1]];
            $dancers[$args[0]] = $b;
            $dancers[$args[1]] = $a;
        } elseif ($cmd === 'p') {
            // partner (exchange by value, really)
            $posA = strpos($dancers, $args[0]);
            $posB = strpos($dancers, $args[1]);
            $a = $dancers[$posA];
            $b = $dancers[$posB];
            $dancers[$posA] = $b;
            $dancers[$posB] = $a;
        }
    }
}

if ($iterations > 100) {
    // determine how many iterations it takes to loop
    $modulo = 0;
    do {
        dance();
        $modulo++;
    } while ($dancers !== $starting);
    $iterations %= $modulo;
}

for ($i = 0; $i < $iterations; $i++) {
    dance();
}

echo "\nfinal: {$dancers}\n";