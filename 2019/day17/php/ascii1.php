<?php

require_once 'intputerv5.php';

$input = trim(file_get_contents($argv[1]));
$input = explode(",", $input);
$program = array_map('intval', $input);

define('MAP_NEWLINE', 10);
define('MAP_OPEN_SPACE', 46);
define('MAP_SCAFFOLDING', 35);

define('MAP_BOT_UP', ord('^'));
define('MAP_BOT_DOWN', ord('v'));
define('MAP_BOT_LEFT', ord('<'));
define('MAP_BOT_RIGHT', ord('>'));
define('MAP_BOT_ADRIFT', ord('>'));

$map = [];
$adj = [];
$curr_y = 0;
$curr_x = 0;
$max_x = 0;
$bot_x = -1;
$bot_y = -1;
$bot_facing = -1;

function updateMap(int $val): void {
    global $map, $adj, $curr_y, $curr_x, $max_x;
    global $bot_x, $bot_y, $bot_facing;

    switch ($val) {
        case MAP_NEWLINE:
            $curr_x = 0;
            $curr_y++;
            return;

        case MAP_OPEN_SPACE:
            break;

        case MAP_BOT_ADRIFT:
            $bot_x = $curr_x;
            $bot_y = $curr_y;
            break;

        case MAP_BOT_UP:
        case MAP_BOT_DOWN:
        case MAP_BOT_LEFT:
        case MAP_BOT_RIGHT:
            $bot_x = $curr_x;
            $bot_y = $curr_y;
            $bot_facing = $val;
        case MAP_SCAFFOLDING:
            updateAdjacent($curr_x - 1, $curr_y);
            updateAdjacent($curr_x + 1, $curr_y);
            updateAdjacent($curr_x, $curr_y - 1);
            updateAdjacent($curr_x, $curr_y + 1);
            break;

        default:
            echo "Encountered unknown value: {$val}\n";
    }

    if (!isset($map[$curr_y])) {
        $map[$curr_y] = [];
    }
    $map[$curr_y][$curr_x] = $val;

    $max_x = max($max_x, $curr_x);
    $curr_x++;
}

function updateAdjacent(int $x, int $y): void {
    global $adj;

    if (!isset($adj[$y])) {
        $adj[$y] = [];
    }
    $adj[$y][$x] = ($adj[$y][$x] ?? 0) + 1;
}

function readMap(int $x, int $y): int {
    global $map;
    return $map[$y][$x] ?? MAP_OPEN_SPACE;
}

function onScaffolding(int $x, int $y): bool {
    $pos = readMap($x, $y);
    return in_array($pos, [MAP_SCAFFOLDING, MAP_BOT_UP, MAP_BOT_DOWN, MAP_BOT_LEFT, MAP_BOT_RIGHT], true);
}

$puter = new IntPuterV5;
$puter->loadProgram($program);
$puter->setOutputCallback('updateMap');
$puter->run();

foreach ($map as $row) {
    foreach ($row as $code) {
        echo chr($code);
    }
    echo "\n";
}

$total = 0;
foreach ($adj as $y => $row) {
    foreach ($row as $x => $count) {
        if ($count === 4 && onScaffolding($x, $y)) {
            $alignment = $x * $y;
            $total += $alignment;
            echo "Alignment at {$x},{$y}\n";
        }
    }
}
echo $total;
