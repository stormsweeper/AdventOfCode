<?php

require_once 'moon.php';

$input = trim(file_get_contents($argv[1]));
$input = explode("\n", $input);

$iterations = intval($argv[2] ?? '0');

$moons = [];
$moons[] = new Moon('Io', $input[0]);
$moons[] = new Moon('Europa', $input[1]);
$moons[] = new Moon('Ganymede', $input[2]);
$moons[] = new Moon('Callisto', $input[3]);

for ($i = 1; $i <= $iterations; $i++) {
    for ($a = 0; $a < 4; $a++) {
        for ($b = $a + 1; $b < 4; $b++) {
            $moons[$a]->applyGravity($moons[$b]);
        }
        $moons[$a]->applyVelocity();
    }
}


echo $moons[0]->totalEnergy() + $moons[1]->totalEnergy() + $moons[2]->totalEnergy() + $moons[3]->totalEnergy();
