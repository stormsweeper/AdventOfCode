<?php

define('MAP_OPEN', '.');
define('MAP_TREES', '|');
define('MAP_LUMBERYARD', '#');

function xytoi($x, $y): int { return $GLOBALS['max_x'] * $y + $x; }
function itoxy($i): array { return [$i % $GLOBALS['max_x'], floor($i / $GLOBALS['max_x'])]; }

function printForest() {
    global $forest, $max_x;
    return implode("\n", str_split($forest, $max_x)) . "\n";
}

$forest = array_filter(explode("\n", rtrim(file_get_contents($argv[1]))));
$max_y = count($forest);
$max_x = strlen($forest[0]);
$forest = implode($forest);
$max_acres = strlen($forest);

$max_mins = intval($argv[2]);

$offsets = [-1, 0, 1];

//echo "Initial state:\n";
//echo printForest();

$previous = [];
for ($mins = 1; $mins <= $max_mins; $mins++) {
    // long enough amd we'll loop
    if (isset($previous[$forest])) {
        echo "found repeat at {$previous[$forest]}\n";
        $repeat = $previous[$forest];
        $remainder = $max_mins - $repeat;
        $mod = $mins - $repeat - 1;
        $find = $repeat + $remainder%$mod;
        $final = array_search($find, $previous);
        echo substr_count($final, MAP_TREES) * substr_count($final, MAP_LUMBERYARD) . "\n";
        exit;
    }
    // count the adjacents
    $adj_trees = $adj_yards = [];
    for ($i = 0; $i < $max_acres; $i++) {
        $acre = $forest[$i];
        [$x, $y] = itoxy($i);
        foreach ($offsets as $dx) {
            foreach ($offsets as $dy) {
                if (
                    // don't add to the center square
                    ($dx === 0 && $dy === 0)
                    ||
                    // out of x bounds
                    ($x + $dx < 0) || ($x + $dx >= $max_x)
                    ||
                    // out of y bounds
                    ($y + $dy < 0) || ($y + $dy >= $max_y)
                ) {
                    continue;
                }
                $adj_i = xytoi($x + $dx, $y + $dy);
                if ($acre === MAP_TREES) {
                    $adj_trees[$adj_i] = ($adj_trees[$adj_i] ?? 0) + 1;
                }
                if ($acre === MAP_LUMBERYARD) {
                    $adj_yards[$adj_i] = ($adj_yards[$adj_i] ?? 0) + 1;
                }
            }
        }
    }

    // set up next gen
    $next_forest = $forest;
    for ($i = 0; $i < $max_acres; $i++) {
        $acre = $forest[$i];
        [$x, $y] = itoxy($i);
        $adj_tree_count = $adj_trees[$i] ?? 0;
        $adj_yard_count = $adj_yards[$i] ?? 0;
        if ($acre === MAP_OPEN && $adj_tree_count >= 3) {
            $next_forest[$i] = MAP_TREES;
        }
        if ($acre === MAP_TREES && $adj_yard_count >= 3) {
            $next_forest[$i] = MAP_LUMBERYARD;
        }
        if ($acre === MAP_LUMBERYARD && ($adj_tree_count < 1 || $adj_yard_count < 1)) {
            $next_forest[$i] = MAP_OPEN;
        }
    }
    $previous[$forest] = $mins - 1;
    $forest = $next_forest;
    //echo "\nAfter {$mins} minutes:\n";
    //echo printForest();
}

echo substr_count($forest, MAP_TREES) * substr_count($forest, MAP_LUMBERYARD);