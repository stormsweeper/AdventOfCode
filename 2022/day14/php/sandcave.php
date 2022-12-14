<?php

$blocked = [];

define('ABYSS', '~');
define('ROCK', '#');
define('SAND', 'o');

function block_node(int $x, int $y, string $type): void {
    global $blocked;
    if (!isset($blocked[$x])) $blocked[$x] = [];
    $blocked[$x][$y] = $type;
}
function blocking_node(int $x, int $y): string|false {
    global $blocked;
    return $blocked[$x][$y]??false;
}
function is_blocked(int $x, int $y): bool {
    global $blocked;
    return blocking_node($x, $y) !== false;
}
function is_abyss(int $x, int $y): bool {
    global $blocked;
    return $y >= PHP_INT_MAX || !isset($blocked[$x]) || blocking_node($x, $y) === ABYSS;
}
function drop_point_y(int $x, int $y): int {
    global $blocked;
    $dpy = PHP_INT_MAX;
    foreach ($blocked[$x]??[] as $dy => $_) {
        if ($dy > $y) $dpy = min($dpy, $dy - 1);
    }
    return $dpy;
}

function print_area(): void {
    global $blocked, $min_x, $max_x, $max_y, $drop_start_x;
    $top_line = str_repeat('.', $max_x - $min_x + 3);
    $top_line[ $drop_start_x - $min_x + 1 ] = '+';
    echo $top_line . "\n";
    for ($y = 1; $y <= $max_y + 1; $y++) {
        for ($x = $min_x - 1; $x <= $max_x + 1; $x++) echo blocking_node($x, $y)?:'.';
        echo "\n";
    }
}

// set up the scene
$drop_start_x = 500;
$sand_dropped = 0;
$min_x = $max_x = $drop_start_x;
$max_y = 1;
$scan = fopen($argv[1], 'r');
while (($rocks = fgets($scan)) !== false) {
    $rocks = trim($rocks);
    if (!$rocks) continue;
    $rocks = array_map(
        function($r) {return array_map('intval', explode(',', $r));},
        explode(' -> ', $rocks)
    );
    $num_rocks = count($rocks);
    for ($i = 0; $i < $num_rocks - 1; $i++) {
        [$x1, $y1] = $rocks[$i];
        [$x2, $y2] = $rocks[$i+1];
        $x_start = min($x1, $x2);
        $x_end = max($x1, $x2);
        $y_start = min($y1, $y2);
        $y_end = max($y1, $y2);
        for ($x = $x_start; $x <= $x_end; $x++) {
            $min_x = min($min_x, $x);
            $max_x = max($max_x, $x);
            for ($y = $y_start; $y <= $y_end; $y++) {
                $max_y = max($max_y, $y);
                block_node($x, $y, ROCK);
            }
        }
    }
}

// drop some sand
$settled_sand = 0;

while ($settled_sand < 1000) {
    $sand_x = $drop_start_x;
    $sand_y = 0;
    while (true) {
        // fall to oblivion?
        if (is_abyss($sand_x, $sand_y)) {
            //echo "is abyss?\n";
            break 2;
        }
        // drop down
        elseif (!is_blocked($sand_x, $sand_y + 1)) {
            //echo "can go down?\n";
            $sand_y = drop_point_y($sand_x, $sand_y);
            continue;
        }
        // drop left
        elseif (!is_blocked($sand_x - 1, $sand_y + 1)) {
            //echo "can go left?\n";
            $sand_x--;
            $sand_y = drop_point_y($sand_x, $sand_y);
            continue;
        }
        // drop right
        elseif (!is_blocked($sand_x + 1, $sand_y + 1)) {
            //echo "can go right?\n";
            $sand_x++;
            $sand_y = drop_point_y($sand_x, $sand_y);
            continue;
        }
        //echo "hmm {$sand_x} {$sand_y}\n";
        block_node($sand_x, $sand_y, SAND);
        $settled_sand++;
        break;
    }
}

echo $settled_sand;