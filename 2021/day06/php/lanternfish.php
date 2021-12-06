<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode(',', $inputs);

// these are almost certainly going to be args for part 2
// how many days to make a new one
$gestation = 7;

// how many extra days bebes need
$immaturity = 2;

// how many days to run the sim for
$sim_length = 80;

// only tracking the sums of the stages
$fishies = array_fill(0, $gestation + $immaturity, 0);

foreach ($inputs as $age) {
    $fishies[$age]++;
}

for ($d = 0; $d < $sim_length; $d++) {
    // this will be the 0 index
    $birthers = array_shift($fishies);
    $fishies[$gestation - 1] += $birthers;
    $fishies[] = $birthers;
}

echo array_sum($fishies);