<?php

ini_set('memory_limit', '2G');

class CupRing {
    public array $cups = [];
    public int $current = -1;
    function __construct(string $labels) {
        $labels = array_map('intval', str_split($labels));
        $this->current = $labels[0];
        $this->splice(1e6, $labels);
    }

    function cupgen(): int {
        static $label = 10;
        return $label++;
    }

    function next(int $from) {
        if (!isset($this->cups[$from])) {
            $this->cups[$from] = $this->cupgen();
        }
        return  $this->cups[$from];
    }

    function nextThree(int $from): array {
        $next = [];
        $next[] =  $this->next($from);
        $next[] = $this->next($next[0]);
        $next[] = $this->next($next[1]);
        return $next;
    }

    function splice(int $from, array $labels): void {
        $end = $this->next($from);
        $last = $from;
        foreach ($labels as $label) {
            $this->cups[$last] = $label;
            $last = $label;
        }
        $this->cups[$last] = $end;
    }

    function destination(array $skip): int {
        $target = $this->current - 1;
        if ($target < 1) $target = 1e6;
        while (in_array($target, $skip, true)) {
            $target--;
            if ($target < 1) $target = 1e6;
        }
        return $target;
    }

    function advanceCurrent(): void {
        $this->current = $this->next($this->current);
    }

    function takeTurn(): void {
        // pop off chain
        $chain = $this->nextThree($this->current);
        $this->cups[$this->current] = $this->next($chain[2]);
        // get dest
        $dest = $this->destination($chain);
        //splice in chain
        $this->splice($dest, $chain);
        // advance
        $this->advanceCurrent();
    }

}

$ring = new CupRing($argv[1]);

for ($t = 1; $t <= 1e7; $t++) {
    if (($t % 1e6) === 0) echo "Taking turn {$t}...\n";
    $ring->takeTurn();
}


list($a, $b) = $ring->nextThree(1);
$p = $a * $b;
echo "{$a} * {$b} = {$p}\n";
