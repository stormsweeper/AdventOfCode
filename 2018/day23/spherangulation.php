<?php

class Sphere {
    public $x = 0, $y = 0, $z = 0, $radius = 0;
    public function __construct($x, $y, $z, $radius) {
        $this->x = $x; $this->y = $y; $this->z = $z; $this->radius = $radius;
    }

    public function distanceFrom(Sphere $other) {
        return (abs($this->x - $other->x) + abs($this->y - $other->y) + abs($this->z - $other->z));
    }

    public function overlaps(Sphere $other) {
        $dist = $this->distanceFrom($other);
        return $dist <= ($this->radius + $other->radius);
    }

    public function numOverlapping($spheres) {
        $overlapping = array_filter(
            $spheres,
            function(Sphere $s) { return $this->overlaps($s); }
        );
        return count($overlapping);
    }

    public function __toString() {
        return "Sphere(x:{$this->x},y:{$this->y},z:{$this->z},r:{$this->radius})";
    }

    public function divide() {
        $half_r = floor($this->radius / 2);
        $reduced_r = floor($this->radius * .75);
        $divided = [];
        foreach ([-1, 1] as $mx) {
            foreach ([-1, 1] as $my) {
                foreach ([-1, 1] as $mz) {
                    $divided[] = new Sphere(
                        $this->x + $mx * $half_r,
                        $this->y + $my * $half_r,
                        $this->z + $mz * $half_r,
                        $reduced_r
                    );
                }
            }
        }
        return $divided;
    }
}

$input = array_filter(array_map('trim', file($argv[1])));

$min_x = $min_y = $min_z = PHP_INT_MAX;
$max_x = $max_y = $max_z = 0 - PHP_INT_MAX;

// pos=<0,0,0>, r=4
function mapBots($line) {
    global $min_x, $min_y, $min_z, $max_x, $max_y, $max_z;
    $r = '/^pos=<(?<x>-?\d+),(?<y>-?\d+),(?<z>-?\d+)>, r=(?<radius>-?\d+)$/';
    preg_match($r, $line, $m);
    $m = array_map('intval', $m);

    // hellz yeah I'm doing this
    foreach (['min', 'max'] as $fn) {
        foreach (['x', 'y', 'z'] as $axis) {
            $keep = "{$fn}_{$axis}";
            ${$keep} = $fn(${$keep}, $m[$axis]);
        }
    }

    return new Sphere($m['x'], $m['y'], $m['z'], $m['radius']);
}

$bots = array_map('mapBots', $input);

// make the starting sphere, and divide it
$sx = round(($min_x + $max_x)/2);
$sy = round(($min_y + $max_y)/2);
$sz = round(($min_z + $max_z)/2);
$sr = floor(max(
        abs($min_x - $max_x),
        abs($min_y - $max_y),
        abs($min_z - $max_z)
    )/2 * 1.25);
$search = (new Sphere($sx, $sy, $sz, $sr))->divide();

$max = 100000;
do {
    $most_bots = array_reduce(
        $search,
        function($carry, Sphere $item) use ($bots) {
            $count = $item->numOverlapping($bots);
            if (!isset($carry) || $count > $carry) {
                return $count;
            }
            return $carry;
        }
    );
    $next = [];
    foreach ($search as $s) {
        $count = $s->numOverlapping($bots);
        if ($count < $most_bots) {
            continue;
        }
        $next = array_merge($next, $s->divide());
    }
    $search = $next;
} while (($search[0])->radius > 0 && --$max);


$found = $search[0];

echo $found->distanceFrom(new Sphere(0, 0, 0, 1));

