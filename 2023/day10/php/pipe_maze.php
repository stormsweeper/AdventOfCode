<?php

$maze = trim(file_get_contents($argv[1]));
$width = strpos($maze, "\n");
$maze = str_replace("\n", '', $maze);
$height = strlen($maze)/$width;

$start_i = strpos($maze, 'S');
// type, i, dist
$start_node = ['S', $start_i, 0];

$connecting = [
    'S' => [
        'up' => ['|', '7', 'F'],
        'down' => ['|', 'L', 'J'],
        'left' => ['-', 'L', 'F'],
        'right' => ['-', '7', 'J'],
    ],
    '|' => [
        'up' => ['|', '7', 'F'],
        'down' => ['|', 'L', 'J'],
        'left' => [],
        'right' => [],
    ],
    '-' => [
        'up' => [],
        'down' => [],
        'left' => ['-', 'L', 'F'],
        'right' => ['-', '7', 'J'],
    ],
    '7' => [
        'up' => [],
        'down' => ['|', 'L', 'J'],
        'left' => ['-', 'L', 'F'],
        'right' => [],
    ],
    'F' => [
        'up' => [],
        'down' => ['|', 'L', 'J'],
        'left' => [],
        'right' => ['-', '7', 'J'],
    ],
    'L' => [
        'up' => ['|', '7', 'F'],
        'down' => [],
        'left' => [],
        'right' => ['-', '7', 'J'],
    ],
    'J' => [
        'up' => ['|', '7', 'F'],
        'down' => [],
        'left' => ['-', 'L', 'F'],
        'right' => [],
    ],
];

function pos2i(int $x, int $y): string {
    global $width;
    return ($y * $width) + $x;
}

function i2pos(int $i): array {
    global $width;
    return [$i%$width, floor($i/$width)];
}

function pipe_at(int $x, int $y): string {
    global $width, $height, $maze;
    if ($x < 0 || $x >= $width || $y < 0 || $y >= $height) return '.';
    return $maze[pos2i($x, $y)];
}

function sort_pipes(array $a, array $b) {
    // reverse sorting as pop is faster than shift
    $by_dist = $b[2] <=> $a[2];
    if ($by_dist === 0) return $b[1] <=> $a[1];
    return $by_dist;
}

function connecting_neighbors(string $type, int $i): array {
    global $connecting;
    $neighbors = [];
    [$x, $y] = i2pos($i);
    $up = pipe_at($x, $y - 1);
    if (in_array($up, $connecting[$type]['up'])) $neighbors['up'] = [$up, pos2i($x, $y - 1)];
    $down = pipe_at($x, $y + 1);
    if (in_array($down, $connecting[$type]['down'])) $neighbors['down'] = [$down, pos2i($x, $y + 1)];
    $left = pipe_at($x - 1, $y);
    if (in_array($left, $connecting[$type]['left'])) $neighbors['left'] = [$left, pos2i($x - 1, $y)];
    $right = pipe_at($x + 1, $y);
    if (in_array($right, $connecting[$type]['right'])) $neighbors['right'] = [$right, pos2i($x + 1, $y)];
    return $neighbors;
}



$visited = [];
$consider = [$start_i => $start_node];

do {
    uasort($consider, 'sort_pipes');

    // get the shortest node
    [$c_type, $c_i, $c_dist] = $current = array_pop($consider);

    foreach (connecting_neighbors($c_type, $c_i) as [$n_type, $n_i]) {
        if (isset($visited[$n_i])) continue;
        // get shortest distance
        $n_dist = min($consider[$n_i][2] ?? PHP_INT_MAX, $c_dist + 1);
        $consider[$n_i] = [$n_type, $n_i, $n_dist];
    }
    // mark current node as visited
    $visited[$c_i] = $current;
} while ($consider);

uasort($visited, 'sort_pipes');

[, , $f_dist] = $furthest = array_shift($visited);

echo "p1: {$f_dist}\n";
