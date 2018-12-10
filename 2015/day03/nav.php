<?php

$dirs = trim(file_get_contents($argv[1]));
$end = strlen($dirs);
$houses = ['0,0' => 0];

$x = 0;
$y = 0;

for ($i = 0; $i < $end; $i++) {
    if ($dirs[$i] === '^') {
        $y++;
    }
    if ($dirs[$i] === 'v') {
        $y--;
    }
    if ($dirs[$i] === '>') {
        $x++;
    }
    if ($dirs[$i] === '<') {
        $x--;
    }
    $house = "{$x},{$y}";
    $houses[$house] = ($houses[$house] ?? 0) + 1;
}

echo count($houses);
