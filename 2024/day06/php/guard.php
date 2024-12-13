<?php

$lab = explode("\n", trim(file_get_contents($argv[1])));

enum Facing {
    case N;
    case E;
    case S;
    case W;

    function right(): Facing {
        return match ($this) {
            Facing::N => Facing::E,
            Facing::E => Facing::S,
            Facing::S => Facing::W,
            Facing::W => Facing::N,
        };
    }
}

class LabGuard {
    private $lab = [];
    private $visited = [];
    private $seen = [];
    private $lab_height = 0;
    private $lab_width = 0;
    private $guard_x = -1;
    private $guard_y = -1;
    private $facing = Facing::N;

    function __construct(array $lab) {
        $this->lab = $lab;
        $this->lab_height = count($lab);
        $this->lab_width = strlen($lab[0]);

        for ($y = 0; $y < $this->lab_height; $y++) {
            for ($x = 0; $x < $this->lab_width; $x++) {
                if ($lab[$y][$x] === '^') {
                    $this->guard_x = $x;
                    $this->guard_y = $y;
                }
            }
        }

        $this->mark_visited($this->guard_x, $this->guard_y);
        $this->look_ahead($this->guard_x, $this->guard_y);
    }

    function visit(): void {
        $this->visited[pos2key($this->x, $this->y)] = true;
    }

    function move(): bool {
        // get next pos
        [$nx, $ny] = $this->next($this->guard_x, $this->guard_y);

        // if oob or not blocked, advance to it
        if (!$this->blocked($nx, $ny)) {
            $this->guard_x = $nx;
            $this->guard_y = $ny;
            // mark visited
            $this->mark_visited($nx, $ny);
            // look ahead
            $this->look_ahead($nx, $ny);
        }
        // else turn right
        else {
            $this->facing = $this->facing->right();
        }

        return $this->in_lab($this->guard_x, $this->guard_y);
    }

    function mark_visited(int $x, int $y): void {
        if ($this->in_lab($x, $y)) {
            $this->visited["{$x},{$y}"] = true;
        }
    }

    function mark_seen(int $x, int $y): void {
        if ($this->in_lab($x, $y)) {
            $this->seen["{$x},{$y}"] = true;
        }
    }

    function look_ahead(int $x, int $y): void {
        while ($this->in_lab($x, $y) && !$this->blocked($x, $y)) {
            $this->mark_seen($x, $y);
            [$x, $y] = $this->next($x, $y);
        }
    }

    function next(int $x, int $y): array {
        return match ($this->facing) {
            Facing::N => [$x,       $y - 1],
            Facing::E => [$x + 1,   $y],
            Facing::S => [$x,       $y + 1],
            Facing::W => [$x - 1,   $y],
        };
    }

    function blocked(int $x, int $y): bool {
        if (!$this->in_lab($x, $y)) return false;

        return $this->lab[$y][$x] === '#';
    }

    function in_lab(int $x, int $y): bool {
        return $x >= 0 && $x < $this->lab_width && $y >= 0 && $y < $this->lab_height;
    }

    function visited(): array {
        return $this->visited;
    }
}

$guard = new LabGuard($lab);

$steps = 0;
while($guard->move() && $steps++ < 10000) {}

$p1 = count($guard->visited());
echo "p1: {$p1}\n";

