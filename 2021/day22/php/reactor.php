<?php

#  ¯\_(ツ)_/¯
ini_set('memory_limit', '2G');

$input = trim(file_get_contents($argv[1]));

preg_match_all('/(on|off) x=(-?\d+)\.\.(-?\d+),y=(-?\d+)\.\.(-?\d+),z=(-?\d+)\.\.(-?\d+)/', $input, $reboot_steps, PREG_SET_ORDER);

function pos2key(int $x, int $y, int $z): string {
    return "{$x},{$y},{$z}";
}

$cubes = [];
foreach ($reboot_steps as [$inst, $on_off, $x1, $x2, $y1, $y2, $z1, $z2]) {
    //echo "{$inst}\n";
    for ($x = $x1; $x <= $x2; $x++) {
        if (abs($x) > 50) continue;
        for ($y = $y1; $y <= $y2; $y++) {
            if (abs($y) > 50) continue;
            for ($z = $z1; $z <= $z2; $z++) {
                if (abs($z) > 50) continue;
                $k = pos2key($x, $y, $z);
                if ($on_off === 'on') {
                    $cubes[$k] = 1;
                }
                else {
                    unset($cubes[$k]);
                }
            }
        }
    }
}

echo count($cubes);