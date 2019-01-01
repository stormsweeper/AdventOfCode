<?php

ini_set('memory_limit', '1G');

$input = file($argv[1]);

[,$depth] = explode(': ', $input[0]);
$depth = intval($depth);

[,$target_coords] = explode(': ', $input[1]);
$target_coords = array_map('intval', explode(',', $target_coords));

$geo_indices = [];
$erosion_levels = [];

function geologicIndex($x, $y) {
    global $depth, $target_coords, $geo_indices;

    //The region at 0,0 (the mouth of the cave) has a geologic index of 0.
    if ($x === 0 && $y === 0) {
        return 0;
    }

    //The region at the coordinates of the target has a geologic index of 0.
    if ([$x,$y] === $target_coords) {
        return 0;
    }

    //If the region's Y coordinate is 0, the geologic index is its X coordinate times 16807.
    if ($y === 0) {
        return $x * 16807;
    }

    //If the region's X coordinate is 0, the geologic index is its Y coordinate times 48271.
    if ($x === 0) {
        return $y * 48271;
    }

    //Otherwise, the region's geologic index is the result of multiplying the erosion levels of the regions at X-1,Y and X,Y-1.
    $key = "{$x},{$y}";
    if (!isset($geo_indices[$key])) {
        $geo_indices[$key] = erosionLevel($x - 1, $y) * erosionLevel($x, $y - 1);
    }
    return $geo_indices[$key];
}

function erosionLevel($x, $y) {
    global $depth, $target_coords, $erosion_levels;
    $key = "{$x},{$y}";
    if (!isset($erosion_levels[$key])) {
        $erosion_levels[$key] = (geologicIndex($x, $y) + $depth) % 20183;
    }
    return $erosion_levels[$key];
}

function dangerLevel($x, $y) {
    return erosionLevel($x, $y) % 3;
}

define('TOOL_TORCH', 'torch');
define('TOOL_GEAR', 'gear');
define('TOOL_NONE', 'none');
define('TERR_ROCKY', 0);
define('TERR_WET', 1);
define('TERR_NARROW', 2);

$tools_by_terr = [
    TERR_ROCKY  => [TOOL_TORCH, TOOL_GEAR],
    TERR_WET    => [TOOL_NONE, TOOL_GEAR],
    TERR_NARROW => [TOOL_NONE, TOOL_TORCH],
];

class CaveNode {
    public $x, $y, $tool, $cost, $prox;
    public function __construct($x, $y, $tool, $cost) {
        global $target_coords;
        $this->x = $x; $this->y = $y; $this->tool = $tool; $this->cost = $cost;
        $this->prox = sqrt(pow($x - $target_coords[0], 2) + pow($y - $target_coords[1], 2));
    }

    public function __toString() {
        return "{$this->x},{$this->y};{$this->tool};{$this->cost}";
    }

    public function key() {
        return "{$this->x},{$this->y};{$this->tool}";
    }

    public function weight() {
        return $this->cost + $this->prox;
    }
}



$start = new CaveNode(0, 0, TOOL_TORCH, 0);
$end = new CaveNode($target_coords[0], $target_coords[1], TOOL_TORCH, INF);

$visited = [];
$consider = [];
$consider[$start->key()] = $start;
$consider[$end->key()] = $end;

function sortNodes(CaveNode $a, CaveNode $b) {
    // reverse sorting as pop is faster than shift
    return $b->weight() <=> $a->weight();
}

do {
    uasort($consider, 'sortNodes');

    // get the shortest node/path, then get neighbors
    $node   = array_pop($consider);

    if ($node->key() === $end->key()) {
        $end = $node;
        break;
    }

    $up     = [$node->x, $node->y - 1];
    $down   = [$node->x, $node->y + 1];
    $left   = [$node->x - 1, $node->y];
    $right  = [$node->x + 1, $node->y];

    foreach ([$up, $down, $left, $right] as $next_coords) {
        // skip if out of bounds
        if (
            $next_coords[0] < 0 || $next_coords[1] < 0 || $next_coords[0] > 4 * $target_coords[0] || $next_coords[1] > 4 * $target_coords[1]
        ) {
            continue;
        }

        // determine which tools to expand for these coords, start and target is always torch
        if ($next_coords === [0, 0] || $next_coords === [$target_coords]) {
            $next_tools = [TOOL_TORCH];
        }
        else {
            $next_terrain = dangerLevel($next_coords[0], $next_coords[1]);
            $next_tools = $tools_by_terr[$next_terrain];
        }

        // which tools are currently valid
        $current_terrain = dangerLevel($node->x, $node->y);
        $current_tools = $tools_by_terr[$current_terrain];

        // needs to be a valid tool for this tile AND next
        $tools = array_intersect($next_tools, $current_tools);

        foreach ($tools as $next_tool) {
            $step = $node->tool === $next_tool ? 1 : 8;
            $next_node = new CaveNode($next_coords[0], $next_coords[1], $next_tool, $step + $node->cost);

            #echo "looking at node: {$next_node}\n";

            // don't go backwards
            if (isset($visited[$next_node->key()])) {
                #echo "Node already visited\n";
                continue;
            }

            $before = $consider[$next_node->key()] ?? null;
            if (isset($before)) {
                #echo "Node already being considered\n";
                // if the new path is shorter, update this node
                if ($before->cost > $next_node->cost) {
                    #echo "prev saw {$before}, updating to {$next_node}\n";
                    $consider[$next_node->key()] = $next_node;
                }
                else {
                    #echo "prev saw {$before}, will not update to {$next_node}\n";
                }
            } else {
                // add the unseen node
                #echo "Adding node to consider\n";
                $consider[$next_node->key()] = $next_node;
            }
        }
    }

    // now mark $node as visited as we've connected to all possible neighbors
    $visited[$node->key()] = $node;

} while (!empty($consider));

echo $end;
