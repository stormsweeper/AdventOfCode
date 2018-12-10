<?php

class Point {
    public $x_pos = 0;
    public $y_pos = 0;
    public $x_vel = 0;
    public $y_vel = 0;

    public static function parse($line) {
        //position=< 9,  1> velocity=< 0,  2>
        $r = '/position=<\s*(?<x_pos>-?\d+),\s*(?<y_pos>-?\d+)> velocity=<\s*(?<x_vel>-?\d+),\s*(?<y_vel>-?\d+)>/';
        if (preg_match($r, $line, $m)) {
            $point = new Point();
            $point->x_pos = intval($m['x_pos']);
            $point->y_pos = intval($m['y_pos']);
            $point->x_vel = intval($m['x_vel']);
            $point->y_vel = intval($m['y_vel']);
            return $point;
        }
        return null;
    }

    public function move() {
        $this->x_pos += $this->x_vel;
        $this->y_pos += $this->y_vel;
    }
}

class PointGrid {
    private $points;
    private $cluster_size;
    private $moves = 0;

    public function __construct($input) {
        $this->points = array_filter(array_map('Point::parse', $input));
        $this->cluster_size = count($this->points) / 3;
    }

    public function move() {
        foreach ($this->points as $point) {
            $point->move();
        }
        $this->moves++;
    }

    public function __toString() {
        $active = [];
        foreach ($this->points as $point) {
            $active[$point->x_pos] = $active[$point->x_pos] ?? [];
            $active[$point->x_pos][$point->y_pos] = '#';
        }

        $out = '';
        [$min_x, $min_y, $max_x, $max_y] = $this->bounds();
        for ($y = $min_y - 1; $y <= $max_y + 1; $y++) {
            for ($x = $min_x - 1; $x <= $max_x + 1; $x++) {
                $out .= $active[$x][$y] ?? '.';
            }
            $out .= "\n";
        }

        return "moves: {$this->moves}\n" . $out;
    }

    public function isClustered() {
        [$width, $height] = $this->dims();
        return $width <= $this->cluster_size && $height <= $this->cluster_size;
    }

    public function dims() {
        [$min_x, $min_y, $max_x, $max_y] = $this->bounds();
        return [$max_x - $min_x, $max_y - $min_y];
    }

    public function bounds() {
        $min_x = $min_y = PHP_INT_MAX;
        $max_x = $max_y = 0 - PHP_INT_MAX;
        foreach ($this->points as $point) {
            $min_x = min($min_x, $point->x_pos);
            $min_y = min($min_y, $point->y_pos);
            $max_x = max($max_x, $point->x_pos);
            $max_y = max($max_y, $point->y_pos);
        }
        return [$min_x, $min_y, $max_x, $max_y];
    }
}
$points = new PointGrid(file($argv[1]));

while (!$points->isClustered()) {
    $points->move();
}

do {
    echo $points;
    $points->move();
} while ($points->isClustered());



