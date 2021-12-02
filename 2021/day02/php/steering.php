<?php

$commands = fopen($argv[1], 'r');

define('FORWARD', 'forward');
define('DOWN', 'down');
define('UP', 'up');

$posY = $posX = 0;

// p2
$aim = 0;
$depth = 0;

while (($cmd = fgets($commands)) !== false) {
    list($dir, $val) = explode(' ', $cmd);
    $val = intval($val);
    if (!$val) continue;
    if ($dir === FORWARD) {
        $posX += $val;
        $d = $depth + ($val * $aim);
        $depth = max(0, $d);
    }
    elseif ($dir === UP) {
        $posY = max(0, $posY - $val);
        $aim -= $val;
    }
    elseif ($dir === DOWN) {
        $posY += $val;
        $aim += $val;
    }
}

$res = $posX * $posY;
echo "part 1: {$res}\n";

$res2 = $posX * $depth;
echo "part 2: {$res2}\n";
