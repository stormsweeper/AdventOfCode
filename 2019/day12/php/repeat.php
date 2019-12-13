<?php

require_once 'moon.php';

$input = trim(file_get_contents($argv[1]));
$input = explode("\n", $input);

$iterations = 0;

$moons = [];
$moons[] = new Moon('Io', $input[0]);
$moons[] = new Moon('Europa', $input[1]);
$moons[] = new Moon('Ganymede', $input[2]);
$moons[] = new Moon('Callisto', $input[3]);


// where the velocities of an axis are all at 0 - this will be the exact opposite position from their starting position
$antipodes = [];

while (!isset($antipodes['vx']) || !isset($antipodes['vy']) || !isset($antipodes['vz'])) {
    $iterations++;
    $tvx = $tvy = $tvz = 0;
    for ($a = 0; $a < 4; $a++) {
        for ($b = $a + 1; $b < 4; $b++) {
            $moons[$a]->applyGravity($moons[$b]);
        }
        $moons[$a]->applyVelocity();
        $tvx += abs($moons[$a]->vx);
        $tvy += abs($moons[$a]->vy);
        $tvz += abs($moons[$a]->vz);
    }
    if ($tvx === 0 && !isset($antipodes['vx'])) {
        $antipodes['vx'] = $iterations;
    }
    if ($tvy === 0 && !isset($antipodes['vy'])) {
        $antipodes['vy'] = $iterations;
    }
    if ($tvz === 0 && !isset($antipodes['vz'])) {
        $antipodes['vz'] = $iterations;
    }
}

echo gmp_lcm(
    $antipodes['vx'],
    gmp_lcm(
        $antipodes['vy'],
        $antipodes['vz']
    )
) * 2;


