<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n\n", $inputs);

$numbers = explode(',', array_shift($inputs));

// precompute the 10 possible winning states
$win_permutations = [];
for ($a = 0; $a < 5; $a++) {
    $r = $c = [];
    for ($b = 0; $b < 5; $b++) {
        $r[] = "{$a},{$b}"; // [0,0 0,0 0,2 0,3 0,4] ...
        $c[] = "{$b},{$a}"; // [0,0 1,0 2,0 3,0 4,0] ...
    }
    $win_permutations[] = $r;
    $win_permutations[] = $c;
}

class BingoBoard {
    // will be val => pos, complete
    private $unmarked = [];

    // sparse array, will be val => pos
    private $marked = [];

    private $last_marked = -1;

    // sum of all values
    private $total = -1;
    // sum of all unmarked values
    private $unmarked_sum = -1;

    private $is_winner = false;


    static function parse_board(string $raw): BingoBoard {
        $b = new static;
        $vals = preg_split('/\s+/s', $raw);
        if (count($vals) < 25) throw new RuntimeException('wtf');
        $b->total = $b->unmarked_sum = array_sum($vals);
        $vals = array_chunk($vals, 5);
        for ($r = 0; $r < 5; $r++) {
            for ($c = 0; $c < 5; $c++) {
                $val = $vals[$r][$c];
                $pos = "{$r},{$c}";
                $b->unmarked[$val] = $pos;
            }
        }
        return $b;
    }

    function markNumber(string $number): void {
        $found = $this->unmarked[$number] ?? false;
        if ($found !== false) {
            $this->marked[$number] = $found;
            asort($this->marked);
            unset($this->unmarked[$number]);
            $this->last_marked = $number;
            $this->unmarked_sum -= $number;
        }
    }

    function isWinner(): bool {
        if (!$this->is_winner) {
            global $win_permutations;
            foreach ($win_permutations as $w) {
                $intersect = array_intersect($w, $this->marked);
                if (count($intersect) === 5) {
                    $this->is_winner = true;
                    break;
                }
            }
        }
        return $this->is_winner;
    }

    function score(): int {
        return $this->unmarked_sum * $this->last_marked;
    }

    function print(): string {
        $grid = array_fill(0,5,'     ');
        foreach ($this->marked as $pos) {
            list($r,$c) = explode(',', $pos);
            $grid[$r][$c] = 'X';
        }
        $grid = implode("\n", $grid);
        return "{$grid}\n";
    }
}

$boards = array_map('BingoBoard::parse_board', $inputs);

$first_winner = -1;
$last_winner = -1;
foreach ($numbers as $num) {
    foreach ($boards as $bid => $board) {
        // skip winning boards
        if ($board->isWinner()) continue;

        $board->markNumber($num);
        if ($board->isWinner()) {
            if ($first_winner === -1) $first_winner = $bid;
            $last_winner = $bid;
        }
    }
}

echo "first: {$boards[$first_winner]->score()} last: {$boards[$last_winner]->score()}\n";

