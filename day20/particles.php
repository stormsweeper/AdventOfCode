<?php

require_once __DIR__ . '/classes.php';

$input = explode("\n", file_get_contents($argv[1]));
$moves = intval($argv[2] ?? 5);
$particles = [];

foreach ($input as $id => $spec) {
    $particles[] = Particle::fromString($id, $spec);
}

function sortParticles(Particle $a, Particle $b) {
    return $a->abscompare($b);
}

usort($particles, 'sortParticles');

for ($i = 0; $i < $moves; $i++) {
    foreach ($particles as $particle) {
        $particle->move();
    }
    usort($particles, 'sortParticles');
}

print_r($particles[0]);