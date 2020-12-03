<?php

$map = trim(file_get_contents($argv[1]));
$map = explode("\n", $map);
$max_x = strlen($map[0]);
$max_y = count($map);

function tree_at(int $x, int $y): bool {
    global $map, $max_x, $max_y;
    $x %= $max_x; // normalize x
    return ($map[$y][$x] ?? -1) === '#';
}

function trees_hit(int $sx, int $sy) {
    global $max_y;
    $cx = $cy = 0;
    $trees_hit = 0;
    
    while ($cy < $max_y) {
        $cx += $sx; $cy += $sy;
        $trees_hit += (int)tree_at($cx, $cy);
    }
    
    return $trees_hit;
}

$part1 = trees_hit(3, 1);
echo "Part 1: {$part1} \n";

$part2 = trees_hit(1, 1) * trees_hit(3, 1) * trees_hit(5, 1) * trees_hit(7, 1) * trees_hit(1, 2) ;
echo "Part 2: {$part2} \n";
