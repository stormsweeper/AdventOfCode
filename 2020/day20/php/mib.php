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