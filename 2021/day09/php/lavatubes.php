<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

define('INPUT_WIDTH', strlen($inputs[0]));
define('INPUT_HEIGHT', count($inputs));

define('HIGH_POINT', 'H');
define('LOW_POINT', 'L');
define('BASIN_POINT', 'B');

$heightmap = [];

function pos2key(int $x, int $y): string {
    return "{$x},{$y}";
}
function key2pos(string $key): array {
    list($x,$y) = explode(',', $key);
    return [intval($x), intval($y)];
}

for ($y = 0; $y < INPUT_HEIGHT; $y++) {
    for ($x = 0; $x < INPUT_WIDTH; $x++) {
        $k = pos2key($x,$y);
        $val = $inputs[$y][$x];

        if ($val === 0) {
            $heightmap[$k] = LOW_POINT;
            // these will get overridden later if they need to be
            if ($x+1 < INPUT_WIDTH) $heightmap[pos2key($x+1, $y)] = BASIN_POINT;
            if ($y+1 < INPUT_HEIGHT) $heightmap[pos2key($x, $y+1)] = BASIN_POINT;
            continue;
        }

        if ($val === 9) {
            $heightmap[$k] = HIGH_POINT;
            continue;
        }

        $is_low = true;
        // check right
        if ($x+1 < INPUT_WIDTH) {
            $rval = $inputs[$y][$x+1];
            $rcmp = $val <=> $rval;
            // i.e. this one is not smaller
            if ($rcmp > -1) $is_low = false;
            // i.e. the right one is not smaller
            if ($rcmp < 1) $heightmap[pos2key($x+1, $y)] = BASIN_POINT;
        }
        // check down
        if ($y+1 < INPUT_HEIGHT) {
            $dval = $inputs[$y+1][$x];
            $dcmp = $val <=> $dval;
            // i.e. this one is not smaller
            if ($dcmp > -1) $is_low = false;
            // i.e. the down one is not smaller
            if ($dcmp < 1) $heightmap[pos2key($x, $y+1)] = BASIN_POINT;
        }
        if (!isset($heightmap[$k])) {
            $heightmap[$k] = $is_low ? LOW_POINT : BASIN_POINT;
        }
    }
}

$risk = 0;
$basins = [];
foreach ($heightmap as $k => $val) {
    if ($val === LOW_POINT) {
        list($x,$y) = key2pos($k);
        $risk += 1 + $inputs[$y][$x];

    }
}


echo $risk;