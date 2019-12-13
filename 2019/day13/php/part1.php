<?php

require_once 'intputerv5.php';

class Cabinet {
    const TILE_EMPTY    = 0;
    const TILE_WALL     = 1;
    const TILE_BLOCK    = 2;
    const TILE_PADDLE   = 3;
    const TILE_BALL     = 4;

    public $screen = [];
    public $max_x = 0;
    public $max_y = 0;

    function peek(int $x, int $y): int {
        return $this->screen[$y][$x] ?? self::TILE_EMPTY;
    }

    function poke(int $x, int $y, int $tile): void {
        $this->max_x = max($this->max_x, $x);
        $this->max_y = max($this->max_y, $x);

        if (!isset($this->screen[$y])) {
            $this->screen[$y] = [];
        }
        $this->screen[$y][$x] = $tile;
    }

    function handleInput(int $input): void {
        static $queue = [];
        $queue[] = $input;
        if (count($queue) === 3) {
            //echo "poking...\n";
            $this->poke(...$queue);
            $queue = [];
        }
    }
}

$input = trim(file_get_contents($argv[1]));
$input = explode(",", $input);
$program = array_map('intval', $input);

$puter = new IntPuterV5();
$puter->loadProgram($program);

$cab = new Cabinet;
$puter->setOutputCallback([$cab, 'handleInput']);

$puter->run();

$blocks = 0;
foreach ($cab->screen as $row) {
    foreach ($row as $tile) {
        if ($tile === Cabinet::TILE_BLOCK) {
            $blocks++;
        }
    }
}

echo $blocks;
