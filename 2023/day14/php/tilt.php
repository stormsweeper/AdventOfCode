<?php

$platform = explode("\n", trim(file_get_contents($argv[1])));

$num_cycles = intval($argv[2]??0);

function rotate_platform(array &$platform): void {
    $rotated = [];
    for ($x = 0; $x < strlen($platform[0]); $x++) {
        $r_row = '';
        foreach ($platform as $row) {
            $r_row = $row[$x] . $r_row;
        }
        $rotated[] = $r_row;
    }
    $platform = $rotated;
}

function tilt_platform(array &$platform): void {
    foreach ($platform as &$row) {
        $row = explode('#', $row);
        // . is < O in ASCII/UTF-8
        foreach ($row as &$chunk) {
            $chunk = str_split($chunk);
            sort($chunk);
            $chunk = implode($chunk);
        }
        $row = implode('#', $row);
    }
}

function calculate_load(array $platform): int {
    $load = 0;
    foreach ($platform as $row) {
        for ($i = 0; $i < strlen($row); $i++) {
            if ($row[$i] === 'O') $load += $i + 1;
        }
    }
    return $load;
}

// start by orienting N to the right
rotate_platform($platform);

if (!$num_cycles) {
    tilt_platform($platform);
    echo calculate_load($platform) . "\n";
    exit;
}

$prev_states = [];
for ($c = 1; $c <= $num_cycles; $c++) {

    for ($r = 0; $r < 4; $r++) {
        tilt_platform($platform);
        rotate_platform($platform);
    }

    $found = array_search($platform, $prev_states, true);
    if ($found !== false) {
        // echo "found loop at {$c} ({$found})\n";
        $loop_size = $c - $found - 1;
        $end_pos = ($num_cycles-$c)%$loop_size + $found;
        // echo "loop size: {$loop_size} end pos: {$end_pos}\n";
        $platform = $prev_states[$end_pos];
        break;
    } else {
        // echo "not in loop at {$c}\n";
    }
    // echo $c . "\t" . calculate_load($platform) . "\n";
    $prev_states[] = $platform;
}

echo calculate_load($platform) . "\n";
