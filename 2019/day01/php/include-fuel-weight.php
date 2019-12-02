<?php

$input = trim(file_get_contents($argv[1]));
$input = explode("\n", $input);


function calcFuel($mass) {
    $mass = intval($mass);
    $fuel = floor($mass / 3) - 2;
    return max(0, $fuel);
}

$required_fuel = array_map(
    function($module) {
        $module_fuel = calcFuel($module);
        $extra_mass = $module_fuel;

        do {
            $additional_fuel = calcFuel($extra_mass);
            $module_fuel += $additional_fuel;
            $extra_mass = $additional_fuel;
        } while ($extra_mass);

        return $module_fuel;
    },
    $input
);
echo array_sum($required_fuel);