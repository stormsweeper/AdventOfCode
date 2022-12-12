<?php

$monkeys = trim(file_get_contents($argv[1]));
$monkeys = explode("\n\n", $monkeys);
$num_rounds = intval($argv[2]??20);
$calming = intval($argv[3]??3);

class Monkey {
    static function create(string $data) { return new self($data); }
    function __construct(string $data) {
        $data = explode("\n", $data);
        $this->items = array_map('intval', explode(', ', substr($data[1], 18)));
        $op = substr($data[2], 19);
        if ($op === 'old * old') {
            $this->op = 'sq';
        }
        else {
            $this->op = $op[4];
            $this->arg = substr($op, 6);
        }
        $this->mod = intval(substr($data[3], 21));
        $this->yes = intval(substr($data[4], 29));
        $this->no = intval(substr($data[5], 30));
        $this->inspected = 0;
    }

    function takeTurn() {
        global $monkeys, $calming, $max_worry;
        while($this->items) {
            $this->inspected++;
            $item = array_shift($this->items);
            // increase worry
            if ($this->op === 'sq') $item  = $item*$item;
            if ($this->op === '+')  $item += $this->arg;
            if ($this->op === '*')  $item *= $this->arg;
            if ($item > $max_worry) $item %= $max_worry;
            // lower worry
            $item = floor($item/$calming);
            // throw item
            if ($item >= $this->mod && $item%$this->mod === 0) {
                $monkeys[$this->yes]->items[] = $item;
            } else {
                $monkeys[$this->no]->items[] = $item;
            }
        }
    }
}

$monkeys = array_map('Monkey::create', $monkeys);
$num_monkeys = count($monkeys);
$max_worry = array_product(array_map(
    function($m) {return $m->mod;},
    $monkeys
));

for ($r = 0; $r < $num_rounds; $r++) {
    for ($m = 0; $m < $num_monkeys; $m++) {
        $monkeys[$m]->takeTurn();
    }
}

$insp = array_map(
    function($m) {return $m->inspected;},
    $monkeys
);

rsort($insp);

echo $insp[0] * $insp[1];