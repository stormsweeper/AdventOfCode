<?php

require_once 'intputerv5.php';
require_once 'repairdroid.php';

$input = trim(file_get_contents($argv[1]));
$input = explode(",", $input);
$program = array_map('intval', $input);

$puter = new IntPuterV5;
$puter->loadProgram($program);

$droid = new RepairDroid;
$puter->setInputCallback([$droid, 'nextMove']);
$puter->setOutputCallback([$droid, 'handleOutput']);

$puter->run();

$droid->printMap();

list (, $oxydist) = $droid->readMap($droid->oxy_x, $droid->oxy_y);

echo "oxy dist: {$oxydist}\n";


$edge = [[$droid->oxy_x, $droid->oxy_y]];
$mins = 0;

while (!empty($edge)) {
    $next_edge = [];
    foreach ($edge as list ($x, $y)) {
        foreach ($droid->adjacentCoords($x, $y) as list($ax, $ay)) {
            list ($pos, $dist) = $droid->readMap($ax, $ay);
            if ($pos === RepairDroid::POS_TRAVERSABLE) {
                $droid->markMap($ax, $ay, RepairDroid::POS_OXY_GAS, $dist);
                $next_edge[] = [$ax, $ay];
            }
        }
    }
    $mins++;
    $edge = $next_edge;
}

$droid->printMap();
echo $mins - 1;