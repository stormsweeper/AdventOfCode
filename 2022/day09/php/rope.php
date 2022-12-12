<?php

function pos2key(int $x, int $y): string {
    return "{$x},{$y}";
}
function key2pos(string $key): array {
    list($x,$y) = explode(',', $key);
    return [intval($x), intval($y)];
}

$num_knots = intval($argv[2]??2);
$knots = array_fill(0, $num_knots, [0,0]);

$tail_visited = [];

function move_head(int $dx, int $dy): void {
    global $num_knots, $knots, $tail_visited;
    $knots[0][0] += $dx; $knots[0][1] += $dy;
    // work out next move
    for ($k = 1; $k < $num_knots; $k++) {
        $x_dist = $knots[$k - 1][0] - $knots[$k][0];
        $y_dist = $knots[$k - 1][1] - $knots[$k][1];
        if (abs($x_dist) > 1 || abs($y_dist) > 1) {
            // not touching
            if ($y_dist < 0) $knots[$k][1]--;
            if ($y_dist > 0) $knots[$k][1]++;
            if ($x_dist < 0) $knots[$k][0]--;
            if ($x_dist > 0) $knots[$k][0]++;
        }
    }
    $tail = $k - 1;
    $tk = pos2key($knots[$tail][0], $knots[$tail][1]);
    $tail_visited[$tk] = true;
}
move_head(0, 0);

$moves = fopen($argv[1], 'r');

while (($move = fgets($moves)) !== false) {
    $move = trim($move);
    if (!$move) continue;

    $dir = $move[0];
    $dx = $dy = 0;
    if ($dir === 'U') $dy = +1;
    if ($dir === 'D') $dy = -1;
    if ($dir === 'R') $dx = +1;
    if ($dir === 'L') $dx = -1;

    $dist = intval(substr($move, 2));
    for ($s = 0; $s < $dist; $s++) move_head($dx, $dy);
}

echo count($tail_visited);