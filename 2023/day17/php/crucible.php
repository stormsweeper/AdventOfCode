<?php

$city = explode("\n", trim(file_get_contents($argv[1])));
$max_y = count($city) - 1;
$max_x = strlen($city[0]) - 1;

function is_oob(int $x, int $y): bool {
    global $max_x, $max_y;
    return $x < 0 || $x > $max_x || $y < 0 || $y > $max_y;
}
function heat_loss_at(int $x, int $y): int {
    global $city;
    if (is_oob($x, $y)) return -1;
    return intval($city[$y][$x]);
}

class Crucible {
    const MAX_PUSHES = 3;

    static function create(int $x, int $y, string $dir, int $heat_loss, int $pushes): ?Crucible {
        if (is_oob($x, $y)) return null;
        return new self($x, $y, $dir, $heat_loss, $pushes);
    }

    static function sift(Crucible $a, Crucible $b): int {
        return $b->weight() <=> $a->weight();
    }

    function __construct(
        public int $x, public int $y, public string $dir, public int $heat_loss, public int $pushes
    ) {}

    function hashkey(): string { return "{$this->x},{$this->y};{$this->dir};{$this->pushes}"; }

    function weight(): int {
        global $max_x, $max_y;
        // heat loss + 5 * manhattan distance from start
        $heuristic = 5*($this->x + $this->y);
        return $this->heat_loss + $heuristic;
    }

    function isAtEnd(): bool {
        global $max_x, $max_y;
        return $this->x === $max_x && $this->y === $max_y;
    }

    function forward(): ?Crucible {
        if ($this->pushes >= self::MAX_PUSHES) return null;
        $new_x = $this->x; $new_y = $this->y;
        if ($this->dir === 'U') $new_y--;
        if ($this->dir === 'D') $new_y++;
        if ($this->dir === 'L') $new_x--;
        if ($this->dir === 'R') $new_x++;
        if (is_oob($new_x, $new_y)) return null;
        $new_heat_loss = $this->heat_loss + heat_loss_at($new_x, $new_y);
        return self::create( $new_x, $new_y, $this->dir, $new_heat_loss, $this->pushes + 1); 
    }
    function left():  ?Crucible {
        $new_x = $this->x; $new_y = $this->y;
        if ($this->dir === 'U') { $new_x--; $new_dir = 'L'; }
        if ($this->dir === 'D') { $new_x++; $new_dir = 'R'; }
        if ($this->dir === 'L') { $new_y++; $new_dir = 'D'; }
        if ($this->dir === 'R') { $new_y--; $new_dir = 'U'; }
        if (is_oob($new_x, $new_y)) return null;
        $new_heat_loss = $this->heat_loss + heat_loss_at($new_x, $new_y);
        return self::create( $new_x, $new_y, $new_dir, $new_heat_loss, 1); 
    }
    function right(): ?Crucible {
        $new_x = $this->x; $new_y = $this->y;
        if ($this->dir === 'U') { $new_x++; $new_dir = 'R'; }
        if ($this->dir === 'D') { $new_x--; $new_dir = 'L'; }
        if ($this->dir === 'L') { $new_y--; $new_dir = 'U'; }
        if ($this->dir === 'R') { $new_y++; $new_dir = 'D'; }
        if (is_oob($new_x, $new_y)) return null;
        $new_heat_loss = $this->heat_loss + heat_loss_at($new_x, $new_y);
        return self::create( $new_x, $new_y, $new_dir, $new_heat_loss, 1); 
    }

    function next(): array { return array_filter([$this->forward(),$this->left(),$this->right()]); }
}

$consider = $visited = [];

$start_down = Crucible::create(0, 0, 'D', 0, 0);
$consider[$start_down->hashkey()] = $start_down;
$start_right = Crucible::create(0, 0, 'R', 0, 0);
$consider[$start_right->hashkey()] = $start_right;

$min_heat_loss = PHP_INT_MAX;
do {
    uasort($consider, 'Crucible::sift');

    $crucible = array_pop($consider);
    // echo $crucible->hashkey() . "\n";
    if ($crucible->isAtEnd()) {
        $min_heat_loss = min($min_heat_loss, $crucible->heat_loss);
        break;
    }

    foreach ($crucible->next() as $next) {
        if (isset($visited[$next->hashkey()])) continue;

        $prior = $consider[$next->hashkey()] ?? null;
        if (!isset($prior) || $next->heat_loss < $prior->heat_loss) {
            $consider[$next->hashkey()] = $next;
        }
    }

    $visited[$crucible->hashkey()] = $crucible;

} while (!empty($consider));

echo "{$min_heat_loss}\n";
