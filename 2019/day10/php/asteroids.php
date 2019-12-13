<?php

$input = trim(file_get_contents($argv[1]));

$map = explode("\n", $input);
define('MAX_Y', count($map));
define('MAX_X', strlen($map[0]));
$map = implode($map);

define('EMPTY_SPACE', '.');
define('ASTEROID', '.');

function posToCoords(int $i): array {
    $x = $i % MAX_X;
    $y = intval($i / MAX_X);
    return [$x, $y];
}

class Asteroid {
    static $asteroids = [];
    public $position, $x, $y, $key;
    public $lines_of_sight = [];

    public static function atKey(string $key): Asteroid {
        return self::$asteroids[$key];
    }

    public static function createAtPos(int $pos): void {
        $ast = new Asteroid($pos);        
        self::$asteroids[$ast->key] = $ast;
    }

    function __construct(int $pos) {
        $this->position = $pos;
        list($this->x, $this->y) = posToCoords($pos);
        $this->key = "{$this->x},{$this->y}";
    }

    function distTo(Asteroid $other): array {
        $ydist = $other->y - $this->y;
        $xdist = $other->x - $this->x;
        return [$ydist, $xdist];        
    }

    function slopeTo(Asteroid $other): string {
        list($ydist, $xdist) = $this->distTo($other);
        $gcd = gmp_gcd($ydist, $xdist);
        return ($ydist / $gcd) .'/'. ($xdist / $gcd);        
    }

    function pythaDistTo(Asteroid $other): float {
        list($ydist, $xdist) = $this->distTo($other);
        return sqrt( pow($ydist, 2) + pow($xdist, 2) );
    }

}

for ($i = 0; $i < strlen($map); $i++) {
    if ($map[$i] === '#') {
        Asteroid::createAtPos($i);
    }
}

$keys = array_keys(Asteroid::$asteroids);
$max = count(Asteroid::$asteroids);

foreach ($keys as $key_a) {
    foreach ($keys as $key_b) {
        if ($key_a === $key_b) {
            continue;
        }
        $ast_a = Asteroid::atKey($key_a);
        $ast_b = Asteroid::atKey($key_b);
        $slope = $ast_a->slopeTo($ast_b);
        $current = array_search($slope, $ast_a->lines_of_sight, true);
        if ($current) {
            $ast_curr = Asteroid::atKey($current);
            if ($ast_a->pythaDistTo($ast_b) < $ast_a->pythaDistTo($ast_curr)) {
                unset($ast_a->lines_of_sight[$current]);
                $ast_a->lines_of_sight[ $ast_b->key ] = $slope;
            }
        } else {
            $ast_a->lines_of_sight[ $ast_b->key ] = $slope;
        }
    }
}

$los_counts = array_map(
    function(Asteroid $ast) {
        return count($ast->lines_of_sight);
    },
    Asteroid::$asteroids
);

asort($los_counts);
echo array_pop($los_counts);

