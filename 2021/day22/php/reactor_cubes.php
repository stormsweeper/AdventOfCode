<?php

class Vertex {
    public function __construct(public int $x, public int $y, public int $z) {}

    //     E-------G
    //     /|      /|
    //    / |     / |
    //   F--|----H  |
    //   |  A----|--C
    //   | /     | /
    //   B-------D
    public static function compare(Vertex $a, Vertex $b): int {
        $cy = $a->y <=> $b->y;
        if ($cy !== 0) return $cy;
        $cx = $a->x <=> $b->x;
        if ($cx !== 0) return $cx;
        return $a->z <=> $b->z;
    }
}

class Cuboid {
    public function __construct(public array $vertices) {
        foreach ($vertices as $v) {
            $this->min_x = min($this->min_x??+INF, $v->x);
            $this->max_x = max($this->max_x??-INF, $v->x);
            $this->min_y = min($this->min_y??+INF, $v->y);
            $this->max_y = max($this->max_y??-INF, $v->y);
            $this->min_z = min($this->min_z??+INF, $v->z);
            $this->max_z = max($this->max_z??-INF, $v->z);
        }
    }

    public static function fromString(string $desc): Cuboid {
        preg_match('/x=(-?\d+)\.\.(-?\d+),y=(-?\d+)\.\.(-?\d+),z=(-?\d+)\.\.(-?\d+)/', $desc, $m);
        [, $x_min, $x_max, $y_min, $y_max, $z_min, $z_max] = $m;
        $vertices = [
            new Vertex($x_min, $y_min, $z_min),
            new Vertex($x_max, $y_min, $z_min),
            new Vertex($x_min, $y_max, $z_min),
            new Vertex($x_max, $y_max, $z_min),
            new Vertex($x_min, $y_min, $z_max),
            new Vertex($x_max, $y_min, $z_max),
            new Vertex($x_min, $y_max, $z_max),
            new Vertex($x_max, $y_max, $z_max),
        ];
        return new Cuboid($vertices);
    }

    public function containsVertex(Vertex $ver): bool {
        $in_x = $ver->x >= $this->min_x && $ver->x <= $this->max_x;
        $in_y = $ver->y >= $this->min_y && $ver->y <= $this->max_y;
        $in_z = $ver->z >= $this->min_z && $ver->z <= $this->max_z;
        return $in_x && $in_y && $in_z;
    }

    public function intersects(Cuboid $other): bool {
        foreach ($other->vertices as $v) {
            if ($this->containsVertex($v)) return true;
        }
        return false;
    }

    public function contains(Cuboid $other): bool {
        $contains_any = false;
        foreach ($other->vertices as $v) {
            if (!$this->containsVertex($v)) return false;
            $contains_any = true;
        }
        return $contains_any;
    }

    public function equals(Cuboid $other): bool {
        return empty(array_diff($this->vertices, $other->vertices));
    }

    public function split(Vertex $ver): array {
        if (!$this->containsVertex($ver)) return [$this];
        if (in_array($ver, $this->vertices, true)) return [$this];
        $split = [];
        $split[] = new Cu
    }
}

$input = trim(file_get_contents($argv[1]));
$input = explode("\n", $input);

$a = Cuboid::fromString($input[0]);
$b = Cuboid::fromString($input[1]);