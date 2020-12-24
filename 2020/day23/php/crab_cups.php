<?php

$labels = str_split(trim($argv[1]));
$num_turns = intval($argv[2]);

class Cup {
    public ?Cup $clockwiseCup = null;
    function __construct(public int $label) {}
    function chainEnd(): Cup {return $this->seek(2);}
    function seek(int $num): Cup {
        $found = $this;
        for ($i = 0; $i < $num; $i++) {
            $found = $found->clockwiseCup;
        }
        return $found;
    }

    function sig(): string {
        $sig = '';
        $c = $this->clockwiseCup;
        while (strlen($sig) < 8) {
            $sig .= $c->label;
            $c = $c->clockwiseCup;
        }
        return $sig;
    }

    function destinationCup(): Cup {
        $sig = $this->sig();
        $dest = null;
        $target = $this->label - 1;
        while (!isset($dest)) {
            if ($target < 1) $target = 9;
            $pos = strpos($sig, $target);
            if ($pos !== false) {
                $dest = $this->seek($pos + 1);
            }
            $target--;
        }
        return $dest;
    }
}

$first_cup = $last_cup = $one_cup = null;
foreach ($labels as $label) {
    $next_cup = new Cup(intval($label));
    if (!isset($last_cup)) {
        $first_cup = $next_cup;
    } else {
        $last_cup->clockwiseCup = $next_cup;
    }
    if ($next_cup->label === 1) $one_cup = $next_cup;
    $last_cup = $next_cup;
}

$last_cup->clockwiseCup = $first_cup;
$current_cup = $first_cup;

for ($t = 1; $t <= $num_turns; $t++) {
    // pop off chain
    $chain_start = $current_cup->clockwiseCup;
    $current_cup->clockwiseCup = $chain_start->chainEnd()->clockwiseCup;
    $chain_start->chainEnd()->clockwiseCup = null;

    // find destination cup
    // splice chain after destination
    $dest = $current_cup->destinationCup();
    $chain_start->chainEnd()->clockwiseCup = $dest->clockwiseCup;
    $dest->clockwiseCup = $chain_start;    

    // move current to next clockwise
    $current_cup = $current_cup->clockwiseCup;
}

echo $one_cup->sig();

