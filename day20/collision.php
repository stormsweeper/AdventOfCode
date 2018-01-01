<?php

require_once __DIR__ . '/classes.php';

$input = explode("\n", file_get_contents($argv[1]));
$moves = intval($argv[2] ?? 5);
$particles = [];

foreach ($input as $id => $spec) {
    $particles[] = Particle::fromString($id, $spec);
}

function sortParticles(Particle $a, Particle $b) {
    return $a->relcompare($b);
}

function checkCollisions() {
    global $particles;
    $remove = [];

    for ($i = 1; $i < count($particles); $i++) {
        $prev = $particles[$i - 1];
        $curr = $particles[$i];
        if ($prev->relcompare($curr) === 0) {
            array_push($remove, $i - 1, $i);
        }
    }

    $remove = array_unique($remove);
    foreach ($remove as $p) {
        unset($particles[$p]);
    }

    return count($remove);
}

usort($particles, 'sortParticles');
// input doesn't have it, but it could?
checkCollisions();

for ($i = 0; $i < $moves; $i++) {
    foreach ($particles as $particle) {
        $particle->move();
    }
    usort($particles, 'sortParticles');
    checkCollisions();
}

echo count($particles);