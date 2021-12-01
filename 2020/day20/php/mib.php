<?php
require_once __dir__ .'/MIBTile.php';

$input = file_get_contents($argv[1]);


$tiles = MIBTile::parse_tiles($input);
$tile_ids = array_keys($tiles);
$num_tiles = count($tile_ids);

for ($a = 0; $a < $num_tiles - 1; $a++) {
    for ($b = $a + 1; $b < $num_tiles; $b++) {
        $tiles[$tile_ids[$a]]->calc_compat($tiles[$tile_ids[$b]]);
    }
}

$corner_ids = [];
foreach ($tiles as $tile_id => $tile) {
    if ($tile->num_compatible_sides() === 2) {
        $corner_ids[] = $tile_id;
    }
}

$num_corners = count($corner_ids);
$product = array_product($corner_ids);

echo "num corners: {$num_corners} product: {$product}\n";

/*
In clue:
                  # 
#    ##    ##    ###
 #  #  #  #  #  #   

Binary mask:
00000000000000000010
10000110000110000111
01001001001001001000

Tail / head:
00000000    0000    00000010
10000110    0001    10000111
01001001    0010    01001000
*/

//these are all offset by 1 to account for the frames
$head_mask = [
    [7,1],
    [1,2], [6,2], [7,2], [8,2],
    [2,3], [5,3],
];

$masks = [];

for ($dx = -1; $dx <= 4; $dx++) {
    for ($dy = -1; $dy <= 5; $dy++) {
        $m = [];
        foreach ($head_mask as list($x, $y)) {
            $mx = $x - $dx; $my = $y + $dy;
            if ( $mx < 1 || $mx > 8 || $my < 1 || $my > 8 ) continue;
            $m[] = [$mx, $my];
        }
        $m = array_map(
            function($pos) { return MIBTile::pos2key(...$pos); },
            $m
        );
        $masks[] = $m;
    }
}

$roughness = 0;
$serpents = 0;
foreach ($tiles as $tile_id => $tile) {
    $roughness += $tile->roughness();
    foreach ($masks as $m) {
        $scan = array_intersect_key($m, $tile->grid);
        if ($scan === $m) {
            $serpents++;
            continue 2;
        }
    }
}


$serpents = floor(count($tiles) / 3.75);

$roughness -= $serpents * 15;
echo "Roughness: {$roughness}\n";
