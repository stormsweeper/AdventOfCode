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

$lazerpos = trim($argv[2] ?? 'nope');
if (!isset(Asteroid::$asteroids[$lazerpos])) {
    echo "No asteroid at: {$lazerpos}\n";
    exit(1);
}

// don't blow ourselves up
$home = Asteroid::atKey($lazerpos);

$targets = [];
foreach (Asteroid::$asteroids as $key => $ast) {
    if ($key === $lazerpos) {
        continue;
    }
    list ($ast->ydist, $ast->xdist) = $ast->distTo($home);
    $theta = atan2($ast->ydist, $ast->xdist) - M_PI_2;
    while ($theta < 0) {
        $theta += 2*M_PI;
    }
    $ast->theta = $theta;
    // make theta 0-2π
    $ast->radius = $home->pythaDistTo($ast);
    $targets[$ast->key] = $ast;
}


//print_r($targets['11,12']);
//print_r($targets['12,13']);
//print_r($targets['11,14']);
//print_r($targets['10,13']);

function sortTargets($a, $b) {
    $theta_cmp = $a->theta <=> $b->theta;
    if ($theta_cmp == 0) {
        return $a->radius <=> $b->radius;
    }
    return $theta_cmp;
}

//var_export(sortTargets($targets['12,4'], $targets['12,8']));
//
//exit;
uasort(
    $targets,
    function ($a, $b) {
        $theta_cmp = $a->theta <=> $b->theta;
        if ($theta_cmp == 0) {
            return $a->radius <=> $b->radius;
        }
        return $theta_cmp;
    }
);

$blown_up = 0;
$last_target = null;

while ($blown_up < 200) {
    $target_keys = array_keys($targets);
    foreach ($target_keys as $key) {
        if ($blown_up === 200) {
            break 2;
        }
        $t = $targets[$key];
        // already hit one
        if ($t->theta == ($last_target->theta ?? -1)) {
            continue;
        }
        // FIRE
        $blown_up++;
        echo "Firing lazor at {$key} (#{$blown_up})\n";
        unset($targets[$key]);
        $last_target = $t;
    }
}


