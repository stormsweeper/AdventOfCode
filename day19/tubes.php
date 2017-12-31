<?php

$map = file_get_contents($argv[1]);
$map = explode("\n", $map);

$y = 0;
$x = strpos($map[0], '|');
$dir = 'd';
$letters = '';
$last = null;
$steps = 0;

function isLetter($mapped) {
    return ord($mapped) >= 65 && ord($mapped) <= 90;
}

while (true) {
    $mapped = $map[$y][$x] ?? ' ';
    echo "{$x} {$y} {$dir} {$mapped} {$last}\n";
    if ($mapped === ' ') {
        break;
    }

    if (isLetter($mapped)) {
        $letters .= $mapped;
    }
    if ($mapped !== '+') {
        switch ($dir) {
            case 'u': list($x, $y) = [$x, $y - 1]; break;
            case 'd': list($x, $y) = [$x, $y + 1]; break;
            case 'l': list($x, $y) = [$x - 1, $y]; break;
            case 'r': list($x, $y) = [$x + 1, $y]; break;
        }
    } else {
        if ($dir === 'u' || $dir === 'd') {
            $left  = $map[$y][$x - 1] ?? ' ';
            $right = $map[$y][$x + 1] ?? ' ';
            if ($left === '-' || isLetter($left)) {
                $dir = 'l';
                list($x, $y) = [$x - 1, $y];
            } elseif ($right === '-' || isLetter($right)) {
                $dir = 'r';
                list($x, $y) = [$x + 1, $y];
            }
        } else {
            $up   = $map[$y - 1][$x] ?? ' ';
            $down = $map[$y + 1][$x] ?? ' ';
            if ($up === '|' || isLetter($up)) {
                $dir = 'u';
                list($x, $y) = [$x, $y - 1];
            } elseif ($down === '|' || isLetter($down)) {
                $dir = 'd';
                list($x, $y) = [$x, $y + 1];
            }
        }
    }
    $last = $mapped;
    $steps++;
    // next
    
    // if | or - continue in direction
    // if letter, add to string and continue in direction
    // if + advance and turn
    // if space, stop
}

print_r([$letters, $steps]);