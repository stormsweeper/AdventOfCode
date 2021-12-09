<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

define('INPUT_WIDTH', strlen($inputs[0]));
define('INPUT_HEIGHT', count($inputs));

define('HIGH_POINT', 'H');
define('LOW_POINT', 'L');

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
        $is_low = true;
        // check right
        if ($x+1 < INPUT_WIDTH) {
            $rcmp = $inputs[$y][$x] <=> $cmp = $inputs[$y][$x+1];
            // i.e. this one is not smaller
            if ($rcmp > -1) $is_low = false;
            // i.e. the right one is not smaller
            if ($rcmp < 1) $heightmap[pos2key($x+1, $y)] = HIGH_POINT;
        }
        // check down
        if ($y+1 < INPUT_HEIGHT) {
            $dcmp = $inputs[$y][$x] <=> $cmp = $inputs[$y+1][$x];
            // i.e. this one is not smaller
            if ($dcmp > -1) $is_low = false;
            // i.e. the down one is not smaller
            if ($dcmp < 1) $heightmap[pos2key($x, $y+1)] = HIGH_POINT;
        }
        if (!isset($heightmap[$k])) {
            $heightmap[$k] = $is_low ? LOW_POINT : HIGH_POINT;
        }
    }
}

$risk = 0;
foreach ($heightmap as $k => $val) {
    if ($val === LOW_POINT) {
        list($x,$y) = key2pos($k);
        $risk += 1 + $inputs[$y][$x];
    }
}

echo $risk;