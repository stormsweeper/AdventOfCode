<?php

require_once 'intputerv5.php';

class Paintbot {
    private $x = 0;
    private $min_x = 0;
    private $max_x = 0;
    private $y = 0;
    private $min_y = 0;
    private $max_y = 0;
    private $painted = [];
    private $facing = 3; // N:3, W:2, S:1, E:0
    private $initial_color = 0;

    function __construct(int $initial_square_color = 0) {
        $this->initial_color = $initial_square_color;
    }

    function camera(): int {
        if (isset($this->painted[$this->y][$this->x])) {
            return $this->painted[$this->y][$this->x];
        }
        if ($this->x === 0 && $this->y === 0) {
            return $this->initial_color;
        }
        return 0;
    }

    function handleInput(int $input): void {
        static $mode = 0;
        if ($mode === 0) {
            $this->paintHere($input);
        } else {
            $this->moveBot($input);
        }
        $mode = ($mode + 1)%2;
    }

    function paintHere(int $color): void {
        if (!isset($this->painted[$this->y])) {
            $this->painted[$this->y] = [];
        }
        $this->painted[$this->y][$this->x] = $color;
    }

    function moveBot(int $turn): void {
        // turn
        if ($turn === 1) {
            $this->facing += 1;
        } else {
            $this->facing += 3; // doing a jughandle to avoid negatives
        }
        $this->facing %= 4;
        // now move
        if ($this->facing === 0) {
            $this->y += 1;
        } elseif ($this->facing === 1) {
            $this->x += 1;
        } elseif ($this->facing === 2) {
            $this->y -= 1;
        } elseif ($this->facing === 3) {
            $this->x -= 1;
        }
        // adjust max bounds
        if ($this->x < $this->min_x) {
            $this->min_x = $this->x;
        }
        if ($this->x > $this->max_x) {
            $this->max_x = $this->x;
        }
        if ($this->y < $this->min_y) {
            $this->min_y = $this->y;
        }
        if ($this->y > $this->max_y) {
            $this->max_y = $this->y;
        }
    }

    function paintedSquares(): int {
        $total = 0;
        foreach ($this->painted as $row) {
            $total += count($row);
        }
        return $total;
    }

    function showPainted(): string {
        $curr_x = $this->x;
        $curr_y = $this->y;

        $output = '';
        for ($y = $this->max_y; $y >= $this->min_y; $y--) {
            for ($x = $this->min_x; $x <= $this->max_x; $x++) {
                $this->x = $x; $this->y = $y;
                $output .= $this->camera() ? '#' : '.';
            }
            $output .= "\n";
        }

        $this->x = $curr_x;
        $this->y = $curr_y;

        return $output;
    }
}

$input = trim(file_get_contents($argv[1]));
$input = explode(",", $input);
$program = array_map('intval', $input);

$initial_color = intval($argv[2] ?? '0');

$puter = new IntPuterV5();
$puter->loadProgram($program);

$bot = new Paintbot($initial_color);
$puter->setInputCallback([$bot, 'camera']);
$puter->setOutputCallback([$bot, 'handleInput']);

$puter->run();

echo $bot->paintedSquares() . "\n";
echo $bot->showPainted();



