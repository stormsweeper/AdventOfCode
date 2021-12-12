<?php

$octopi = trim(file_get_contents($argv[1]));
$octopi = str_replace("\n", '', $octopi);
$octopi = str_split($octopi);

$max_steps = intval($argv[2]??100);

function pos2i(int $x, int $y): string {
    return ($y * 10) + $x;
}
function i2pos(int $i): array {
    return [$i%10, floor($i/10)];
}

function adj(int $i): array {
    list($x, $y) = i2pos($i);
    $adj = [];
    foreach ([-1,0,1] as $dy)  {
        foreach ([-1,0,1] as $dx)  {
            $ax = $x + $dx; $ay = $y + $dy;
            if (($ay===$y && $ax===$x) || $ax < 0 || $ay < 0 || $ax > 9 || $ay > 9) {
                continue;
            }
            $adj[] = pos2i($ax, $ay);
        }
    }
    return $adj;
}

function flashing(array $octopi, array $flashed) {
    return array_filter(array_diff_key($octopi, $flashed), function($o) {return $o > 9;});
}

$total_flashes = 0;
$all_flashed = 0;
for ($step = 1; $step <= $max_steps; $step++) {
    $flashed = [];
    $octopi = array_map(function($o) {return $o + 1;}, $octopi);
    while ($flashing = flashing($octopi, $flashed)) {
        foreach ($flashing as $i => $_) {
            $flashed[$i] = true;
            foreach (adj($i) as $a) $octopi[$a] += 1;
        }
    }
    // p2 
    if ($all_flashed === 0 && count($flashed) === 100) {
        $all_flashed = $step;
        break;
    }
    $total_flashes += count($flashed);
    foreach ($flashed as $f => $_) $octopi[$f] = 0;
}

echo "Total:{$total_flashes}\n";
echo "All flashed at step:{$all_flashed}\n";
for ($y = 0; $y < 10; $y++) {
    for ($x = 0; $x < 10; $x++) {
        $i = pos2i($x, $y);
        echo "{$octopi[$i]}";
    }
    echo "\n";
}
