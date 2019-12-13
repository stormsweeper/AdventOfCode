<?php

class Moon {
    public $x, $y, $z;
    public $vx = 0;
    public $vy = 0;
    public $vz = 0;

    function __construct(string $name, string $def) {
        preg_match('/<x=(-?\d+), y=(-?\d+), z=(-?\d+)>/', $def, $m);
        $this->x = intval($m[1]);
        $this->y = intval($m[2]);
        $this->z = intval($m[3]);
    }

    function pos(): string {
        return "{$this->x},{$this->y},{$this->z}";        
    }

    function applyGravity(Moon $other): void {
        $this->applyGravityOnAxis('x', $other);
        $this->applyGravityOnAxis('y', $other);
        $this->applyGravityOnAxis('z', $other);
    }

    function applyGravityOnAxis(string $axis, Moon $other): void {
        $a = $this->{$axis};
        $b = $other->{$axis};
        $vaxis = 'v' . $axis;
        if ($a < $b) {
            $this->{$vaxis}  += 1;
            $other->{$vaxis} -= 1;
        } elseif ($a > $b) {
            $this->{$vaxis}  -= 1;
            $other->{$vaxis} += 1;
        }
    }

    function applyVelocity(): void {
        $this->x += $this->vx;
        $this->y += $this->vy;
        $this->z += $this->vz;
    }

    function potentionalEnergy(): int {
        return abs($this->x) + abs($this->y) + abs($this->z);
    }

    function kineticEnergy(): int {
        return abs($this->vx) + abs($this->vy) + abs($this->vz);
    }

    function totalEnergy():int {
        return $this->potentionalEnergy() * $this->kineticEnergy();
    }
}