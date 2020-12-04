<?php

$batch = trim(file_get_contents($argv[1]));
$batch = str_replace("\n\n", '|', $batch);
$batch = str_replace("\n", ' ', $batch);
$batch = explode('|', $batch);
$batch = array_map(
    function($passport) {
        $pp = [];
        foreach (explode(' ', $passport) as $part) {
            list($k, $v) = explode(':', $part, 2);
            $pp[$k] = $v;
        }
        return $pp;
    },
    $batch
);

$req_keys = [
    'byr',
    'iyr',
    'eyr',
    'hgt',
    'hcl',
    'ecl',
    'pid',
];

$present = array_filter(
    $batch,
    function($passport) use ($req_keys) {
        return empty(
            array_diff($req_keys, array_keys($passport))
        );
    }
);

echo "Passports with req fields present: " . count($present) . "\n";

$valid = array_filter(
    $present,
    function($passport) {
        $valid = true;

        $valid = $valid && $passport['byr'] >= 1920 && $passport['byr'] <= 2002;
        $valid = $valid && $passport['iyr'] >= 2010 && $passport['iyr'] <= 2020;
        $valid = $valid && $passport['eyr'] >= 2020 && $passport['eyr'] <= 2030;
        if ($valid) {
            $unit = substr($passport['hgt'], -2);
            $len = substr($passport['hgt'], 0, -2);
            if ($unit === 'cm') {
                $valid = $len >= 150 && $len <= 193;
            }
            elseif ($unit === 'in') {
                $valid = $len >= 59 && $len <= 76;
            }
            else {
                $valid = false;
            }
        }
        $valid = $valid && preg_match('/^#[0-9a-f]{6}$/', $passport['hcl']);
        $valid = $valid && preg_match('/^(amb|blu|brn|gry|grn|hzl|oth)$/', $passport['ecl']);
        $valid = $valid && preg_match('/^[0-9]{9}$/', $passport['pid']);

        return $valid;
    }
);


echo "Passports with valid fields present: " . count($valid) . "\n";
