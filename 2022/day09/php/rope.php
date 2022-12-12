<?php

function pos2key(int $x, int $y): string {
    return "{$x},{$y}";
}
function key2pos(string $key): array {
    list($x,$y) = explode(',', $key);
    return [intval($x), intval($y)];
}
function manhattanDistance(int $x1, int $y1, int $x2, int $y2): int {
    return abs($x1 - $x2) + abs($y1 - $y2);
}

$head_x = $head_y = $tail_x = $tail_y = 0;
$head_visited = $tail_visited = [];

function move_head(int $dx, int $dy): void {
    global $head_x, $head_y, $tail_x, $tail_y, $tail_visited;
    $head_x += $dx; $head_y += $dy;
    // work out tail move
    $x_dist = $head_x - $tail_x;
    $y_dist = $head_y - $tail_y;
    if (abs($x_dist) > 1 || abs($y_dist) > 1) {
        // not touching
        if ($y_dist < 0) $tail_y--;
        if ($y_dist > 0) $tail_y++;
        if ($x_dist < 0) $tail_x--;
        if ($x_dist > 0) $tail_x++;
    }
    $hk = pos2key($head_x, $head_y);
    $head_visited[$hk] = $head_visited[$hk]??0 + 1;
    $tk = pos2key($tail_x, $tail_y);
    $tail_visited[$tk] = $tail_visited[$tk]??0 + 1;
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