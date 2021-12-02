<?php

$commands = fopen($argv[1], 'r');

define('FORWARD', 'forward');
define('DOWN', 'down');
define('UP', 'up');

$posY = $posX = 0;

while (($cmd = fgets($commands)) !== false) {
    list($dir, $val) = explode(' ', $cmd);
    $val = intval($val);
    if (!$val) continue;
    if ($dir === FORWARD) {
        $posX += $val;
    }
    elseif ($dir === UP) {
        $posY = max(0, $posY - $val);
    }
    elseif ($dir === DOWN) {
        $posY += $val;
    }

}

$res = $posX * $posY;
echo "part 1: {$res}\n";