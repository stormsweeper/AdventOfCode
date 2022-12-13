<?php

$height_map = trim(file_get_contents($argv[1]));
$width = strpos($height_map, "\n");
$height_map = str_replace("\n", '', $height_map);
$start_i = strpos($height_map, "S");
$height_map[$start_i] = 'a';
$end_i = strpos($height_map, "E");
$height_map[$end_i] = 'z';
$grid_size = strlen($height_map);
$length = $grid_size / $width;
// convert to int[]
$height_map = array_map(
    function(string $h): int { return ord($h) - ord('a'); },
    str_split($height_map)
);


function pos2i(int $x, int $y): string {
    global $width;
    return ($y * $width) + $x;
}
function i2pos(int $i): array {
    global $width;
    return [$i%$width, floor($i/$width)];
}
function adj(int $i): array {
    global $width, $length;
    [$x, $y] = i2pos($i);
    $adj = [];

    foreach ([[-1,0], [1,0], [0,-1], [0,1]] as [$dx, $dy])  {
        $ax = $x + $dx; $ay = $y + $dy;
        if ($ax < 0 || $ay < 0 || $ax >= $width || $ay >=$length) {
            continue;
        }
        $adj[] = pos2i($ax, $ay);
    }

    return $adj;
}

class LocNode {
    function __construct(public int $i, public int $dist) {}
}

// working backwards
$consider = [
    $start_i => new LocNode($start_i, PHP_INT_MAX),
    $end_i   => new LocNode($end_i, 0),
];
$visited = [];
$lowest = [];

while ($consider) {
    uasort(
        $consider,
        function(LocNode $a, LocNode $b) {
            return $b->dist <=> $a->dist;
        }
    );
    $node = array_pop($consider);
    $n_elev = $height_map[$node->i];
    foreach (adj($node->i) as $adj) {
        // if we already cleared the node, skip on
        if (isset($visited[$adj])) continue;

        // skip if too low (again, going backwards)
        if ($height_map[$adj] - $height_map[$node->i] < -1) continue;

        if (!isset($consider[$adj])) {
            $consider[$adj] = new LocNode($adj, $node->dist + 1);
        }
        else {
            $consider[$adj]->dist = min($node->dist + 1, $consider[$adj]->dist);
        }
    }
    $visited[$node->i] = true;
    if ($height_map[$node->i] === 0) {
        $lowest[$node->i] = $node->dist;
    }
}

$orig = $lowest[$start_i];
$min = min($lowest);

echo "orig: {$orig} lowest: {$min}\n";