<?php

$lab = explode("\n", trim(file_get_contents($argv[1])));

enum Facing: string {
    case N = 'N';
    case E = 'E';
    case S = 'S';
    case W = 'W';

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
    private $positions = [];
    private $seen = [];
    private $lab_height = 0;
    private $lab_width = 0;
    private $guard_x = -1;
    private $guard_y = -1;
    private $guard_start_x = -1;
    private $guard_start_y = -1;
    private $facing = Facing::N;
    private $first_ob_y = -1;
    private $looping = false;

    function __construct(array $lab) {
        $this->lab = $lab;
        $this->lab_height = count($lab);
        $this->lab_width = strlen($lab[0]);

        // locate guard
        for ($y = 0; $y < $this->lab_height; $y++) {
            for ($x = 0; $x < $this->lab_width; $x++) {
                if ($lab[$y][$x] === '^') {
                    $this->guard_x = $this->guard_start_x = $x;
                    $this->guard_y = $this->guard_start_y = $y;
                }
            }
        }

        // locate first ob
        $nx = $this->guard_start_x; $ny = $this->guard_start_y;
        do {
            [$nx, $ny] = $this->next($nx, $ny);
            if ($this->blocked($nx, $ny)) {
                $this->first_ob_y = $ny;
                break;
            }
        } while ($this->pos_in_bounds($nx, $ny));

        $this->mark_visited($this->guard_x, $this->guard_y);
    }

    function do_rounds(): void {
        while($this->move());
    }

    function move(): bool {
        // get next pos
        [$nx, $ny] = $this->next($this->guard_x, $this->guard_y);

        // if oob or not blocked, advance to it
        if (!$this->blocked($nx, $ny)) {
            $this->guard_x = $nx;
            $this->guard_y = $ny;
            // mark visited
            $this->mark_visited();
        }
        // else turn right
        else {
            $this->facing = $this->facing->right();
        }

        return $this->in_lab() && !$this->looping;
    }

    function mark_visited(): void {
        if ($this->in_lab()) {
            $this->visited["{$this->guard_x},{$this->guard_y}"] = true;
            $pos = "{$this->guard_x},{$this->guard_y},{$this->facing->value}";
            if (isset($this->positions[$pos])) {
                $this->looping = true;
                return;
            }
            $this->positions[$pos] = true;
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
        if (!$this->pos_in_bounds($x, $y)) return false;

        return $this->lab[$y][$x] === '#';
    }

    function pos_in_bounds(int $x, int $y): bool {
        return $x >= 0 && $x < $this->lab_width && $y >= 0 && $y < $this->lab_height;
    }

    function in_lab(): bool {
        return $this->pos_in_bounds($this->guard_x, $this->guard_y);
    }

    function visited(): array {
        return $this->visited;
    }

    function blockable(): array {
        $coords_set = array_map(
            function($coords): array {
                return sscanf($coords, '%d,%d');
            },
            array_keys($this->visited)
        );

        return array_filter(
            $coords_set,
            function($coords): bool {
                [$x, $y] = $coords;
                return !($x === $this->guard_start_x && $y <= $this->guard_start_y && $y >= $this->first_ob_y);
            }
        );
    }
}

$guard = new LabGuard($lab);

$guard->do_rounds();

$p1 = count($guard->visited());
echo "p1: {$p1}\n";

$p2 = 0;
foreach ($guard->blockable() as [$bx, $by]) {
    $vlab = $lab;
    $vlab[$by][$bx] = '#';
    $vguard = new LabGuard($vlab);
    $vguard->do_rounds();
    $p2 += $vguard->in_lab(); 
}
echo "p2: {$p2}\n";

