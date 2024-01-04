<?php

$city = explode("\n", trim(file_get_contents($argv[1])));
$max_y = count($city) - 1;
$max_x = strlen($city[0]) - 1;

define('MIN_PUSHES', intval($argv[2]??1));
define('MAX_PUSHES', intval($argv[3]??3));

function is_oob(int $x, int $y): bool {
    global $max_x, $max_y;
    return $x < 0 || $x > $max_x || $y < 0 || $y > $max_y;
}

function heat_loss_from(int $x, int $y): int {
    global $city;
    if (is_oob($x, $y)) return -1;
    return intval($city[$y][$x]);
}

class Crucible {

    static function create(int $x, int $y, string $dir, int $heat_loss, int $pushes): ?Crucible {
        if (is_oob($x, $y)) return null;
        return new self($x, $y, $dir, $heat_loss, $pushes);
    }

    // reverse sort 
    static function sift(Crucible $a, Crucible $b): int {
        return $b->dist() <=> $a->dist();
    }

    function __construct(
        public int $x, public int $y, public string $dir, public int $heat_loss, public int $pushes
    ) {}

    function poskey(): string { return "{$this->x},{$this->y};{$this->dir}"; }
    function hashkey(): string { return "{$this->x},{$this->y};{$this->dir};{$this->pushes}"; }

    // manhattan distance from end
    function dist(): int { return $this->x + $this->y; }

    function weight(): int {
        global $max_x, $max_y;
        $heuristic = 1 * ($max_x - $this->x + $max_y - $this->y);
        return $this->heat_loss + $heuristic;
    }

    function mayProceed(): bool { return $this->pushes < MAX_PUSHES; }
    function mayTurn(): bool { return $this->pushes >= MIN_PUSHES; }

    function isAtEnd(): bool {
        global $max_x, $max_y;
        return $this->x === $max_x && $this->y === $max_y;
    }

    function forward(): ?Crucible {
        if (!$this->mayProceed()) return null;
        $new_x = $this->x; $new_y = $this->y;
        if ($this->dir === 'U') $new_y--;
        if ($this->dir === 'D') $new_y++;
        if ($this->dir === 'L') $new_x--;
        if ($this->dir === 'R') $new_x++;
        if (is_oob($new_x, $new_y)) return null;
        $new_heat_loss = $this->heat_loss + heat_loss_from($new_x, $new_y);
        return self::create( $new_x, $new_y, $this->dir, $new_heat_loss, $this->pushes + 1); 
    }
    function left():  ?Crucible {
        if (!$this->mayTurn()) return null;
        $new_x = $this->x; $new_y = $this->y;
        if ($this->dir === 'U') { $new_x--; $new_dir = 'L'; }
        if ($this->dir === 'D') { $new_x++; $new_dir = 'R'; }
        if ($this->dir === 'L') { $new_y++; $new_dir = 'D'; }
        if ($this->dir === 'R') { $new_y--; $new_dir = 'U'; }
        if (is_oob($new_x, $new_y)) return null;
        $new_heat_loss = $this->heat_loss + heat_loss_from($new_x, $new_y);
        return self::create( $new_x, $new_y, $new_dir, $new_heat_loss, 1); 
    }
    function right(): ?Crucible {
        if (!$this->mayTurn()) return null;
        $new_x = $this->x; $new_y = $this->y;
        if ($this->dir === 'U') { $new_x++; $new_dir = 'R'; }
        if ($this->dir === 'D') { $new_x--; $new_dir = 'L'; }
        if ($this->dir === 'L') { $new_y--; $new_dir = 'U'; }
        if ($this->dir === 'R') { $new_y++; $new_dir = 'D'; }
        if (is_oob($new_x, $new_y)) return null;
        $new_heat_loss = $this->heat_loss + heat_loss_from($new_x, $new_y);
        return self::create( $new_x, $new_y, $new_dir, $new_heat_loss, 1); 
    }

    function next(): array { return array_filter([$this->forward(),$this->left(),$this->right()]); }
}

$consider = $visited = $min_heat_loss = [];

$start_down = Crucible::create(0, 0, 'D', 0, 0);
$consider[$start_down->hashkey()] = $start_down;
$min_heat_loss[$start_down->poskey()] = 0;

$start_right = Crucible::create(0, 0, 'R', 0, 0);
$consider[$start_right->hashkey()] = $start_right;
$min_heat_loss[$start_right->poskey()] = 0;

$end = null;
do {
    uasort($consider, 'Crucible::sift');

    $crucible = array_pop($consider);
    // echo $crucible->hashkey() . "\n";
    if ($crucible->isAtEnd()) {
        $end = $crucible;
        break;
    }

    foreach ($crucible->next() as $next) {
        if (isset($visited[$next->hashkey()])) continue;

        $prior_heat = $min_heat_loss[$next->poskey()] ?? PHP_INT_MAX;
        if ($next->heat_loss < $prior_heat) {
            $min_heat_loss[$next->poskey()] = $next->heat_loss;

            $consider[$next->hashkey()] = $next;
        }
    }

    $visited[$crucible->hashkey()] = 1;

} while (!empty($consider));

echo "{$end->heat_loss}\n";
