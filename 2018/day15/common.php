<?php

define('MAP_WALL', '#');
define('MAP_FLOOR', '.');
define('MAP_ELF', 'E');
define('MAP_GOBLIN', 'G');
define('DIR_U', 'up');
define('DIR_D', 'down');
define('DIR_L', 'left');
define('DIR_R', 'right');

require_once __DIR__ . '/Critter.php';
require_once __DIR__ . '/BattleMap.php';

function manhattanDistance($x1, $y1, $x2, $y2) {
    return abs($x1 - $x2) + abs($y1 - $y2);
}

function readingOrderCompare($apos, $bpos) {
    [$xa, $ya] = $apos;
    [$xb, $yb] = $bpos;
    $ydiff = $ya - $yb;
    if ($ydiff === 0) {
        return $xa - $xb;
    }
    return $ydiff;
}

function xytoi($x, $y): int { return $GLOBALS['max_x'] * $y + $x; }
function itoxy($i): array { return [$i % $GLOBALS['max_x'], floor($i / $GLOBALS['max_x'])]; }
