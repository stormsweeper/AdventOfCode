<?php

require_once 'intputerv5.php';

class Cabinet {
    const TILE_EMPTY    = 0;
    const TILE_WALL     = 1;
    const TILE_BLOCK    = 2;
    const TILE_PADDLE   = 3;
    const TILE_BALL     = 4;
    const SPRITES = [
        self::TILE_EMPTY    => ' ',
        self::TILE_WALL     => '#',
        self::TILE_BLOCK    => '+',
        self::TILE_PADDLE   => '=',
        self::TILE_BALL     => 'o',
    ];

    public $screen = [];
    public $max_x = 0;
    public $max_y = 0;
    public $score = 0;
    public $x_ball = 0;
    public $x_paddle = 0;

    function peek(int $x, int $y): int {
        return $this->screen[$y][$x] ?? self::TILE_EMPTY;
    }

    function poke(int $x, int $y, int $tile): void {
        $this->max_x = max($this->max_x, $x);
        $this->max_y = max($this->max_y, $y);

        if (!isset($this->screen[$y])) {
            $this->screen[$y] = [];
        }
        $this->screen[$y][$x] = $tile;
        if ($tile === self::TILE_BALL) {
            $this->x_ball = $x;
        } elseif ($tile === self::TILE_PADDLE) {
            $this->x_paddle = $x;
        }
    }

    function joystick(): int {
        $move = $this->x_ball <=> $this->x_paddle;
        echo "moving paddle: {$move}\n";
        return $move;
    }

    function handleInput(int $input): void {
        static $queue = [];
        $queue[] = $input;
        if (count($queue) === 3) {
            list($x, $y, $tile) = $queue;
            if ($x === -1) {
                if ($y !== 0) {
                    throw new RuntimeException('Was expecting score, got: '. json_encode($queue));
                }
                $this->score = max($this->score, $tile);
            } else {
                $this->poke($x, $y, $tile);
            }
            $queue = [];
            //$this->showGame();
        }
    }

    function showGame(): void {
        if (count($this->screen) < 23 || count($this->screen[22] ?? []) < 45) {
            return;
        }

        $field = '';
        foreach ($this->screen as $row) {
            foreach ($row as $tile) {
                $field .= self::SPRITES[$tile];
            }
            $field .= "\n";
        }
        echo $field;
        echo str_repeat('-', 45) . "\n";
        echo "Score: {$this->score}\n";
        echo str_repeat('-', 45) . "\n";
        echo "Controls: ',' for left, '.' for right, anything else not to move\n";
    }
}

$input = trim(file_get_contents($argv[1]));
$input = explode(",", $input);
$program = array_map('intval', $input);

$puter = new IntPuterV5();
$puter->loadProgram($program);

$cab = new Cabinet;
$puter->setInputCallback([$cab, 'joystick']);
$puter->setOutputCallback([$cab, 'handleInput']);

$puter->setRegister(0, 2, 1);
$puter->run();

echo $cab->score;

