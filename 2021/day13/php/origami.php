<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

function pos2key(int $x, int $y): string {
    return "{$x},{$y}";
}
function key2pos(string $key): array {
    list($x,$y) = explode(',', $key);
    return [intval($x), intval($y)];
}

$dots = [];
$mode = 'dots';
$num_folds = 0;

foreach ($inputs as $line) {
    // switch modes
    if ($line === '') {
        $mode = 'folds';
        echo 'Before folding:' . array_sum($dots) . "\n";
        continue;
    }
    if ($mode === 'dots') {
        $dots[$line] = 1;
        continue;
    }

    $line = str_replace('fold along ', '', $line);
    list($axis, $amt) = explode('=', $line);

    $next = [];

    $max_x = $max_y = 0;
    foreach ($dots as $pos => $_) {
        list($x, $y) = key2pos($pos);
        if ($axis === 'x') {
            $x = $amt - abs($x - $amt);
        }
        else {
            $y = $amt - abs($y - $amt);
        }
        $max_x = max($max_x, $x);
        $max_y = max($max_y, $y);
        $next[pos2key($x, $y)] = 1;
    }
    $dots = $next;
    $num_folds++;
    echo "After fold {$num_folds}:" . array_sum($dots) . "\n";
}


for ($y = 0; $y <= $max_y; $y++) {
    for ($x = 0; $x <= $max_x; $x++) {
        echo empty($dots[pos2key($x, $y)]) ? ' ' : '#';
    }
    echo "\n";
}