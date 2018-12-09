<?php

ini_set('memory_limit', '1G');

$num_players = intval($argv[1] ?? 9);
$high_val = intval($argv[2] ?? 25);
$next_val = 0;
$turns = 0;

class MarbleGame {
    private $circle ;

    public function __construct() {
        $this->circle = new SplDoublyLinkedList();
        $this->circle->push(0);
    }

    public function playMarble($val) {
        $this->circle->push( $this->circle->shift() );
        $this->circle->push( $val );
    }

    public function removeMarble() {
        for ($i = 0; $i < 7; $i++) {
            $this->circle->unshift( $this->circle->pop() );
        }
        $removed = $this->circle->pop();
        $this->circle->push( $this->circle->shift() );
        return $removed;
    }
}

function currentPlayer() {
    global $turns, $num_players;
    return ($turns % $num_players) + 1;
}

$marbles = new MarbleGame();

while ($turns < $high_val) {
    $turns++;
    $next_val = $turns;
    $player_idx = currentPlayer();
    if ($next_val % 23 === 0) {
        $removed = $marbles->removeMarble();
        $scores[$player_idx] = $next_val + $removed + ($scores[$player_idx] ?? 0);
    } else {
        $marbles->playMarble($next_val);
    }
    //printMarbles();
}

echo max($scores);