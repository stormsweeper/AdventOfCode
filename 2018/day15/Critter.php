<?php

abstract class Critter {
    protected $id;
    protected $pos_x;
    protected $pos_y;
    protected $attack_power = 3;
    protected $hit_points = 200;

    public function __construct($id, $x, $y) {
        $this->id = $id;
        $this->pos_x = $x;
        $this->pos_y = $y;
    }

    abstract public function type(): string;
    public function enemyType(): string {
        if ($this->type() === MAP_ELF) {
            return MAP_GOBLIN;
        }
        return MAP_ELF;
    }

    public function equals(Critter $other) {
        return $this->id === $other->id &&
            $this->type() === $other->type() &&
            $this->pos_x === $other->pos_x &&
            $this->pos_y === $other->pos_y;
    }

    public function critterId() {
        return $this->id;
    }

    public function coords() {
        return [$this->pos_x, $this->pos_y];
    }

    public function isAlive(): bool {
        return $this->hit_points > 0;
    }

    public function hitPoints() {
        return $this->hit_points;
    }

    public function takeDamage(int $damage) {
        $this->hit_points -= $damage;
    }

    public function enemiesInRange() {
        return BattleMap::getInstance()->adjacentEnemies($this->pos_x, $this->pos_y, $this->enemyType());
    }

    public function move() {
        $map = BattleMap::getInstance();

        $enemies = $this->enemiesInRange();
        if (count($this->enemiesInRange()) > 0) {
            return;
        }

        // move to next step
        $next = $map->bestNextStep($this->mapLocation(), $this->enemyType());
        if (!$next) {
            return;
        }

        $this->pos_x = $next->x;
        $this->pos_y = $next->y;
    }

    public function mapLocation(): BattleMapLocation {
        return BattleMap::getInstance()->itemAt($this->pos_x, $this->pos_y);
    }

    public function attack() {
        $enemies = $this->enemiesInRange();
        if (count($enemies) < 1) {
            return;
        }

        //print_r($enemies);
        // find lowest HP
        if (count($enemies) > 1) {
            usort(
                $enemies,
                function($a, $b) {
                    return $a->hit_points - $b->hit_points;
                }
            );
        }

        $enemy = array_shift($enemies);
        $enemy->hit_points -= $this->attack_power;

        if (!$enemy->isAlive()) {
            echo "removing {$enemy->id} at {$enemy->pos_x},{$enemy->pos_x}\n";
            BattleMap::getInstance()->removeCombatant($enemy);
            BattleMap::getInstance()->printDebug();
        }

    }
}

class Elf extends Critter { public function type(): string { return MAP_ELF; } }
class Goblin extends Critter { public function type(): string { return MAP_GOBLIN; } }