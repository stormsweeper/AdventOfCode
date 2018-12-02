<?php

class Coord
{
    public $x, $y, $z;

    public function __construct(int $x, int $y, int $z) {
        $this->x = $x;
        $this->y = $y;
        $this->z = $z;
    }

    public function add(Coord $other) {
        $this->x += $other->x;
        $this->y += $other->y;
        $this->z += $other->z;
    }

    public function absdistance(): int {
        return abs($this->x) + abs($this->y) + abs($this->z);
    }

    /**
     * @see https://i.pinimg.com/originals/b4/0f/47/b40f47b6b0b7629da45a5df754123cca.jpg
     */
    public function abscompare(Coord $other): int {
        return $this->absdistance() <=> $other->absdistance();
    }

    public function relcompare(Coord $other): int {
        foreach (['x', 'y', 'z'] as $pos) {
            $compare = $this->{$pos} <=> $other->{$pos};
            if ($compare <> 0) {
                return $compare;
            }
        }
        return 0;
    }
}

class Particle
{
    public $id, $position, $velocity, $acceleration;

    public function __construct(int $id, Coord $position, Coord $velocity, Coord $acceleration) {
        $this->id = $id;
        $this->position = $position;
        $this->velocity = $velocity;
        $this->acceleration = $acceleration;
    }

    // e.g. p=< 1,2,3>, v=< 4,5,6>, a=< 7,9,0>
    public static function fromString(int $id, string $spec): Particle {
        $coords = array_map(
            function($coords) {
                $coords = explode(',', substr($coords, 3, -1));
                $coords = array_map('intval', $coords);
                return new Coord($coords[0], $coords[1], $coords[2]);
            },
            explode(', ', $spec)
        );
        return new Particle($id, $coords[0], $coords[1], $coords[2]);
    }

    public function move() {
        $this->velocity->add($this->acceleration);
        $this->position->add($this->velocity);
    }

    public function abscompare(Particle $other): int {
        return $this->position->abscompare($other->position);
    }

    public function relcompare(Particle $other): int {
        return $this->position->relcompare($other->position);
    }
}