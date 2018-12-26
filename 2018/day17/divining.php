<?php

define('SCAN_SPRING', '+');
define('SCAN_CLAY', '#');
define('SCAN_SAND', '.');
define('SCAN_FLOWING', '|');
define('SCAN_STILL', '~');
define('DIR_D', 'down');
define('DIR_L', 'left');
define('DIR_R', 'right');

function parseLine($line) {
    $coords = explode(', ', $line);
    if (strpos($coords[0], 'y=') === 0) {
        $coords = array_reverse($coords);
    }
    return array_map(
        function($dim) {
            $dim = substr($dim, 2);
            if (strpos($dim, '..') !== false) {
                [$start, $end] = explode('..', $dim);
                return range(intval($start), intval($end));
            }
            return [intval($dim)];
        },
        $coords
    );
}

function scanAt($x, $y) {
    global $scan;
    return $scan[$y][$x] ?? SCAN_SAND;
}

function flowWaterTo($x, $y) {
    global $scan;
    if (scanAt($x, $y) === SCAN_SAND) {
        if (!isset($scan[$y])) {
            $scan[$y] = [];
        }
        $scan[$y][$x] = SCAN_FLOWING;
        ksort($scan[$y]);
    }
}

function neighbors($x, $y) {
    return [
        DIR_L => scanAt($x - 1, $y),
        DIR_R => scanAt($x + 1, $y),
        DIR_D => scanAt($x, $y + 1),
    ];
}

function calmWater($y) {
    global $scan;
    $calms = [];
    $clay = array_keys(
        array_filter(
            $scan[$y] ?? [],
            function($sig) { return $sig === SCAN_CLAY; }
        )
    );

    for ($i = 0; $i < count($clay) - 1; $i++) {
        $left = $clay[$i] + 1;
        $right = $clay[$i + 1] - 1;
        if ($left > $right) {
            continue;
        }
        #echo "checking y={$y}: {$left}..{$right}\n";
        for ($x = $left; $x <= $right; $x++) {
            if (scanAt($x, $y) !== SCAN_FLOWING || !hasFloor($x, $y)) {
                continue 2;
            }
        }
        $calms[] = [$left, $right];
    }

    foreach ($calms as [$left, $right]) {
        for ($x = $left; $x <= $right; $x++) {
            $scan[$y][$x] = SCAN_STILL;
        }
    }
    return count($calms);
}

function hasFloor($x, $y) {
    $found = scanAt($x, $y + 1);
    #echo "checking floor below {$x},{$y} - found {$found}\n";
    return $found === SCAN_CLAY || $found === SCAN_STILL;
}

function printScan() {
    global $scan, $min_x, $max_x, $min_y, $max_y;
    $out = '';
    for ($y = 0; $y <= $max_y + 1; $y++) {
        for ($x = $min_x - 1; $x <= $max_x + 1; $x++) {
            $out .= scanAt($x, $y);
        }
        $out .= "\n";
    }
    echo $out;
}

$scanlines = array_filter(array_map('trim', file($argv[1])));
$scanlines = array_map('parseLine', $scanlines);

$scan = ['0' => [500 => SCAN_SPRING]];
$min_y = PHP_INT_MAX;
$max_y = 0;
$min_x = PHP_INT_MAX;
$max_x = 0;

foreach ($scanlines as [$xr, $yr]) {
    foreach ($yr as $y) {
        $min_y = min($min_y, $y);
        $max_y = max($max_y, $y);
        if (!isset($scan[$y])) {
            $scan[$y] = [];
        }
        foreach ($xr as $x) {
            $min_x = min($min_x, $x);
            $max_x = max($max_x, $x);
            $scan[$y][$x] = SCAN_CLAY;
        }
    }
}

//
$current_depth = 0;
$max_depth = $max_y;
// at each depth
while ($current_depth < $max_depth) {
    // left to right, check for water
    $line = $scan[$current_depth] ?? [];
    #echo "line at y={$current_depth}: " . json_encode($line) . "\n";
    $flowed = $calmed = false;
    foreach ($line as $x => $signal) {
        $neighbors = neighbors($x, $current_depth);

        // if +, expand down
        if ($signal === SCAN_SPRING) {
            flowWaterTo($x, $current_depth + 1);
            continue;
        }

        if ($signal === SCAN_FLOWING) {
            // nothing to do here
            if ($neighbors[DIR_D] === SCAN_FLOWING) {
                continue;
            }

            // if | expand down if possible
            if ($neighbors[DIR_D] === SCAN_SAND) {
                flowWaterTo($x, $current_depth + 1);
                continue;
            }
            elseif (hasFloor($x, $current_depth)) {
                // if | expand right if possible
                if (scanAt($x + 1, $current_depth) === SCAN_SAND) {
                    $flowed = true;
                    $max_x = max($max_x, $x + 1);
                    flowWaterTo($x + 1, $current_depth);                
                }
                // if | expand left if possible
                if (scanAt($x - 1, $current_depth) === SCAN_SAND) {
                    $flowed = true;
                    $min_x = min($min_x, $x - 1);
                    flowWaterTo($x - 1, $current_depth);                
                }
            }
            
            
        }
    }
    // if calmed, go back up a depth
    if (calmWater($current_depth)) {
        $current_depth--;
    }
    // else if expanded left stay on depth
    elseif ($flowed) {
        // nada
    }
    // else go deeper
    else {
        $current_depth++;
    }
    
}

$flowing_count = 0;
$still_count = 0;
foreach ($scan as $y => $line) {
    if ($y < $min_y || $y > $max_y) {
        continue;
    }

    $counts = array_count_values($line);
    $flowing_count += ($counts[SCAN_FLOWING] ?? 0);
    $still_count += ($counts[SCAN_STILL] ?? 0);
}

$total_water = $flowing_count + $still_count;
echo "Ended with {$flowing_count} flowing and {$still_count} calm for a total of {$total_water}\n";