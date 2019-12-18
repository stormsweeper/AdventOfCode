<?php

class Position {
}

class RepairDroid {
    // leaving lower 999 for dist
    const POS_UNEXPLORED = 0;
    const POS_TRAVERSABLE = 1;
    const POS_OXY_SYS = 2;
    const POS_OXY_GAS = 8;
    const POS_WALL = 3;
    const SPRITES = [
        self::POS_UNEXPLORED => ' ',
        self::POS_TRAVERSABLE => '.',
        self::POS_OXY_SYS => 'X',
        self::POS_OXY_GAS => 'O',
        self::POS_WALL => '#',
        'origin' => '*',
        'droid' => 'D',
    ];

    const MOVE_BLOCKED = 0;
    const MOVE_ALLOWED = 1;
    const MOVE_TARGET = 2;

    const DIR_N = 1;
    const DIR_S = 2;
    const DIR_W = 3;
    const DIR_E = 4;
    const DIRS = [
        'N' => self::DIR_N,
        'S' => self::DIR_S,
        'W' => self::DIR_W,
        'E' => self::DIR_E,
    ];

    public $x = 0;
    public $y = 0;
    public $last_x = 0;
    public $last_y = 0;
    public $oxy_x = -1;
    public $oxy_y = -1;
    public $map = [[[self::POS_TRAVERSABLE,0]]];
    public $last_move = 0;
    public $rundist = 0;
    public $maxdist = 0;
    public $facing = self::DIR_N;

    static function taxicab(int $x, int $y): int {
        return abs($x) + abs($y);
    }

    function readMap(int $x, int $y): array {
        return $this->map[$y][$x] ?? [self::POS_UNEXPLORED, -1];
    }

    function adjacentCoords(int $x, int $y): array {
        //echo "adj for {$x},{$y}\n";
        return [
            self::DIR_N => [$x, $y + 1],
            self::DIR_S => [$x, $y - 1],
            self::DIR_W => [$x - 1, $y],
            self::DIR_E => [$x + 1, $y],
        ];
    }

    function readAdjacent(int $x, int $y): array {
        //echo "readAdjacent({$x},{$y})\n";
        return array_map(
            function($coords) {
                return $this->readMap($coords[0], $coords[1]);
            },
            $this->adjacentCoords($x, $y)
        );
    }

    function markMap(int $x, int $y, int $pos, int $dist = -1): void {
        list ($cpos, $cdist) = $this->readMap($x, $y);
        if ($cpos !== self::POS_UNEXPLORED && $pos !== self::POS_OXY_GAS && $cpos !== $pos) {
            throw new RuntimeException("Position {$x},{$y} already explored! ({$cpos})");
        }

        $this->maxdist = max($this->maxdist, self::taxicab($x, $y));

        //echo "marking map at {$x},{$y} as {$pos}/{$dist}\n";
        if (!isset($this->map[$y])) {
            $this->map[$y] = [];
        }
        $this->map[$y][$x] = [$pos, $dist];
        if ($dist < $cdist) {
            foreach ($this->adjacentCoords($x, $y) as list($ax, $ay)) {
                list ($apos, $adist) = $this->readMap($ax, $ay);
                if ($dist + 1< $adist && ($apos === self::POS_TRAVERSABLE || $apos === self::POS_OXY_SYS)) {
                    $this->markMap($ax, $ay, $apos, $dist + 1);
                }
            }
        }
    }

    function turnRight(): void {
        if ($this->facing === self::DIR_N) {
            $this->facing = self::DIR_E;
        } elseif ($this->facing === self::DIR_E) {
            $this->facing = self::DIR_S;
        } elseif ($this->facing === self::DIR_S) {
            $this->facing = self::DIR_W;
        } elseif ($this->facing === self::DIR_W) {
            $this->facing = self::DIR_N;
        }
    }

    function nextMove(): int {
        static $total = 0;
        // bail if on oxy sys
        if ($this->x === $this->oxy_x && $this->y === $this->oxy_y) {
            //echo "ON OXY AT: {$this->x},{$this->y}\n";
            //$this->printMap();
            //return 0;
        }

        // look for adjacent unexplored closest to origin
        // else turn right
        $unexplored = [];
        $traversed = [];
        foreach ($this->readAdjacent($this->x, $this->y) as $dir => list ($pos, $dist)) {
            if ($pos === self::POS_UNEXPLORED) {
                $unexplored[] = $dir;
            } elseif ($pos === self::POS_TRAVERSABLE) {
                $traversed[] = $dir;
            }
        }
        shuffle($unexplored); shuffle($traversed);
        $found = array_pop($unexplored) ?? array_pop($traversed);
        if ($found) {
            $this->facing = $found;
        } else {
            echo "umm, turn right?\n";
            $this->turnRight();
        }

        $next_move = $this->facing;
        //echo "next move ({$total}): {$next_move} \n";

        if (($total++ % 100) === 0) {
            //$this->printMap();
            if ($total > 1000000) {
                return 0;
            }
        }
        return $this->last_move = $next_move;
    }

    function nextPos(): array {
        $x = $this->x;
        $y = $this->y;
        if ($this->last_move === self::DIR_N) {
            $y += 1;
        } elseif ($this->last_move === self::DIR_S) {
            $y -= 1;
        } elseif ($this->last_move === self::DIR_W) {
            $x -= 1;
        } elseif ($this->last_move === self::DIR_E) {
            $x += 1;
        }
        return [$x, $y];
    }

    function shortestAdjDist(int $x, int $y): int {
        //echo "looking for shortest adj to {$x},{$y}\n";
        $shortest = PHP_INT_MAX;
        foreach ($this->readAdjacent($x, $y) as $dir => list ($type, $dist)) {
            //echo "adj2 at {$dir} is {$type}/{$dist}\n";
            if ($type === self::POS_OXY_SYS || $type === self::POS_TRAVERSABLE) {
                if ($dist >= 0) {
                    $shortest = min($shortest, $dist);
                }
            }
        }
        return $shortest;
    }

    function handleOutput(int $status): void {
        list (, $cdist) = $this->readMap($this->x, $this->y);
        //echo "starting from {$this->x},{$this->y} ({$cdist})\n";
        list ($next_x, $next_y) = $this->nextPos();
        //echo "status for {$next_x},{$next_y} is {$status}\n";
        if ($status === self::MOVE_BLOCKED) {
            // mark wall
            $this->markMap($next_x, $next_y, self::POS_WALL);
            // print map
            //$this->printMap();
        } elseif ($status === self::MOVE_TARGET) {
            // mark oxy
            $next_dist = $this->shortestAdjDist($next_x, $next_y) + 1;
            $this->markMap($next_x, $next_y, self::POS_OXY_SYS, $next_dist);
            // set oxy coords
            $this->oxy_x = $next_x; $this->oxy_y = $next_y;
            // update pos
            $this->x = $next_x; $this->y = $next_y;
        } else {
            // mark traversable
            $next_dist = $this->shortestAdjDist($next_x, $next_y) + 1;
            $this->markMap($next_x, $next_y, self::POS_TRAVERSABLE, $next_dist);
            // update pos
            $this->x = $next_x; $this->y = $next_y;
        }
    }

    function spriteFor(int $x, int $y): string {
        if ($x === $this->x && $y === $this->y) {
            return self::SPRITES['droid'];
        }
        
        if ($x === 0 && $y === 0) {
            return self::SPRITES['origin'];
        }
        
        list ($pos) = $this->readMap($x, $y);
        return self::SPRITES[$pos];
    }

    function printMap(): void {
        $yrange = range($this->maxdist, 0 - $this->maxdist);
        $xrange = range(0 - $this->maxdist, $this->maxdist);
        $output = '';
        foreach ($yrange as $y) {
            foreach ($xrange as $x) {
                $output .= $this->spriteFor($x, $y);
            }
            $output .= "\n";
        }
        echo $output . "\n";        
    }
}