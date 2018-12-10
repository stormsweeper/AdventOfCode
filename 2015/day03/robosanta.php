<?php

$dirs = trim(file_get_contents($argv[1]));
$end = strlen($dirs);
$houses = ['0,0' => 0];

$x = $y = ['santa' => 0, 'robo-santa' => 0];

for ($i = 0; $i < $end; $i++) {
    if ($i%2 === 0) {
        $sleigh = 'santa';
    } else {
        $sleigh = 'robo-santa';
    }
    if ($dirs[$i] === '^') {
        $y[$sleigh]++;
    }
    if ($dirs[$i] === 'v') {
        $y[$sleigh]--;
    }
    if ($dirs[$i] === '>') {
        $x[$sleigh]++;
    }
    if ($dirs[$i] === '<') {
        $x[$sleigh]--;
    }
    $house = "{$x[$sleigh]},{$y[$sleigh]}";
    $houses[$house] = ($houses[$house] ?? 0) + 1;
}

echo count($houses);
