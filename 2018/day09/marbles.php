<?php

class Marble {
    public $value = 0;
    public $counterClockwise = null;
    public $clockwise = null;

    public function __construct($value = 0, $counterClockwise = null, $clockwise = null) {
        $this->value = $value;
        $this->counterClockwise = $counterClockwise;
        $this->clockwise = $clockwise;
    }

    public function isCurrent() {
        global $current_marble;
        return $current_marble->value === $this->value;
    }

    public function __toString() {
        if ($this->isCurrent()) {
            return "({$this->value})";
        }
        return "{$this->value}";
    }
}

function currentPlayer() {
    global $turns, $num_players;
    return ($turns % $num_players) + 1;
}

function printMarbles() {
    global $zero_marble, $turns;

    if ($turns === 0) {
        echo "[-] (0)";
        return;
    }

    $player_idx = currentPlayer();
    echo "[{$player_idx}] {$zero_marble} ";
    $ptr = $zero_marble;
    while ($ptr->clockwise->value !== 0) {
        echo "{$ptr->clockwise} ";
        $ptr = $ptr->clockwise;
    }
    echo "\n";
}

$zero_marble = new Marble();
$zero_marble->counterClockwise = $zero_marble->clockwise = $zero_marble;
$current_marble = $zero_marble;

$num_players = intval($argv[1] ?? 9);
$high_val = intval($argv[2] ?? 25);
$next_vals = range(1, $high_val);
$scores = [];
$turns = 0;

//printMarbles();
while (!empty($next_vals)) {
    $turns++;
    $next_val = array_shift($next_vals);
    $player_idx = currentPlayer();
    if ($next_val % 23 === 0) {
        $removed = $current_marble->counterClockwise->counterClockwise->counterClockwise->counterClockwise->counterClockwise->counterClockwise->counterClockwise;
        $scores[$player_idx] = $next_val + $removed->value + ($scores[$player_idx] ?? 0);
        $left = $removed->counterClockwise;
        $right = $removed->clockwise;
        $left->clockwise = $right;
        $right->counterClockwise = $left;
        $current_marble = $right;
    } else {
        $left = $current_marble->clockwise;
        $right = $left->clockwise;
        $next_marble = new Marble($next_val, $left, $right);
        $left->clockwise = $next_marble;
        $right->counterClockwise = $next_marble;
        $current_marble = $next_marble;
    }
    $next_val++;
    //printMarbles();
}

echo max($scores);