<?php

require_once __DIR__ . '/common.php';


function test_bestNextStep($map, BattleMapLocation $from, BattleMapLocation $expected) {
    BattleMap::initialize($map);
    $enemy_type = $from->type === MAP_ELF ? MAP_GOBLIN : MAP_ELF;
    $actual = BattleMap::getInstance()->bestNextStep($from, $enemy_type);
    assert(
        $expected->coords() === $actual->coords(),
        "Unit at {$from} expected to go to {$expected}, went to {$actual} instead.\n"
    );
}

function test_bestDest($map, BattleMapLocation $from, BattleMapLocation $expected) {
    BattleMap::initialize($map);
    $enemy_type = $from->type === MAP_ELF ? MAP_GOBLIN : MAP_ELF;
    $actual = BattleMap::getInstance()->bestDest($from, $enemy_type);
    assert(
        $expected->coords() === $actual->location->coords(),
        "Unit at {$from} expected to go to {$expected}, went to {$actual->location} instead.\n"
    );
}

// test 01
echo "## TEST 01\n";
$map_01 = <<<'MAP01'
#######
#E..G.#
#...#.#
#.G.#G#
#######
MAP01;
$from_01 = new BattleMapLocation(1, 1, MAP_ELF);
$expected_01 = new BattleMapLocation(2, 1, MAP_FLOOR);
$expected_01b = new BattleMapLocation(3, 1, MAP_FLOOR);
test_bestNextStep($map_01, $from_01, $expected_01);
test_bestDest($map_01, $from_01, $expected_01b);

// test 02
echo "## TEST 02\n";
$map_02 = <<<'MAP02'
#######
#.E...#
#.....#
#...G.#
#######
MAP02;
$from_02 = new BattleMapLocation(2, 1, MAP_ELF);
$expected_02 = new BattleMapLocation(3, 1, MAP_FLOOR);
$expected_02b = new BattleMapLocation(4, 2, MAP_FLOOR);
test_bestNextStep($map_02, $from_02, $expected_02);
test_bestDest($map_02, $from_02, $expected_02b);

// tests 03
// "Here's a larger example of movement:"
echo "## TEST 03\n";
$map_03_01 = <<<'MAP0301'
#########
#G..G..G#
#.......#
#.......#
#G..E..G#
#.......#
#.......#
#G..G..G#
#########
MAP0301;
$from_03_01 = [
    new BattleMapLocation(1, 1, MAP_GOBLIN),
    new BattleMapLocation(4, 1, MAP_GOBLIN),
    new BattleMapLocation(7, 1, MAP_GOBLIN),
    new BattleMapLocation(1, 4, MAP_GOBLIN),
    new BattleMapLocation(4, 4, MAP_ELF),
    new BattleMapLocation(7, 4, MAP_GOBLIN),
    new BattleMapLocation(1, 7, MAP_GOBLIN),
    new BattleMapLocation(4, 7, MAP_GOBLIN),
    new BattleMapLocation(7, 7, MAP_GOBLIN),
];
$expected_03_01 = [
    new BattleMapLocation(2, 1, MAP_FLOOR),
    new BattleMapLocation(4, 2, MAP_FLOOR),
    new BattleMapLocation(6, 1, MAP_FLOOR),
    new BattleMapLocation(2, 4, MAP_FLOOR),
    new BattleMapLocation(4, 3, MAP_FLOOR),
    new BattleMapLocation(7, 3, MAP_FLOOR),
    new BattleMapLocation(1, 6, MAP_FLOOR),
    new BattleMapLocation(4, 6, MAP_FLOOR),
    new BattleMapLocation(7, 6, MAP_FLOOR),
];
$expected_03_01b = [
    new BattleMapLocation(4, 3, MAP_FLOOR),
    new BattleMapLocation(4, 3, MAP_FLOOR),
    new BattleMapLocation(4, 3, MAP_FLOOR),

    new BattleMapLocation(3, 4, MAP_FLOOR),
    new BattleMapLocation(4, 3, MAP_FLOOR),
    new BattleMapLocation(5, 3, MAP_FLOOR),

    new BattleMapLocation(3, 3, MAP_FLOOR),
    new BattleMapLocation(4, 4, MAP_FLOOR),
    new BattleMapLocation(4, 4, MAP_FLOOR),
];
for ($i = 0; $i < 9; $i++) {
    echo "# test_bestNextStep {$i}\n";
    test_bestNextStep($map_03_01, $from_03_01[$i], $expected_03_01[$i]);
    //echo "# test_bestDest {$i}\n";
    //test_bestDest($map_03_01, $from_03_01[$i], $expected_03_01b[$i]);
}
