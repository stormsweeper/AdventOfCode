<?php

class BattleMap {

    private static $instance;

    protected $cave_map, $max_x, $max_y;

    protected $last_combatant = null;

    protected $rounds = 0, $turns = 0, $num_elfs = 0, $num_goblins = 0;

    protected function xytoi($x, $y): int { return $this->max_x * $y + $x; }
    protected function itoxy($i): array { return [$i % $this->max_x, floor($i / $this->max_x)]; }

    public static function initialize($input) {
        self::$instance = new self($input);
    }

    public static function getInstance(): BattleMap {
        return self::$instance;
    }

    protected function __construct($input) {
        $input = array_filter(explode("\n", rtrim($input)));
        $max_y = count($input);
        $max_x = strlen($input[0]);
        $cave_map = implode($input);
        $cave_map = str_pad($cave_map, $max_x * $max_y, MAP_WALL);
        $this->cave_map = $cave_map;
        $this->max_x = $max_x;
        $this->max_y = $max_x;

        $combatants = [];
        foreach ($this->findCombatantPositions() as [$type, $x, $y]) {
            if ($type === MAP_ELF) {
                $critter_id = 'elf' . $this->num_elfs++;
                $critter_class = 'Elf';
            } else {
                $critter_id = 'goblin' . $this->num_goblins++;
                $critter_class = 'Goblin';
            }
        
            $combatants[$critter_id] = new $critter_class($critter_id, $x, $y);
        }
        $this->combatants = $combatants;
    }

    protected function findCombatantPositions($type = null) {
        $types = $type ?: MAP_ELF . MAP_GOBLIN;
        preg_match_all("/[{$types}]/", $this->cave_map, $matches, PREG_OFFSET_CAPTURE);
        $combatants = [];
        foreach ($matches[0] as $m) {
            $index = $m[1];
            $type = $m[0];
            [$x, $y] = $this->itoxy($m[1]);
            $combatants[] = [$type, $x, $y];
        }
        return $combatants;
    }

    public function currentScore() {
        if ($this->hasCombatants()) {
            return 0;
        }

        $total_hp = array_sum(
            array_map(
                function($c) {
                    return $c->hitPoints();
                },
                $this->combatants
            )
        );
        echo "total hp: {$total_hp}\n";
        return $total_hp * $this->completedRounds();
    }

    public function elapsedTurns() {
        return $this->turns;
    }

    public function completedRounds() {
        return $this->rounds;
    }

    public function hasCombatants($type = null) {
        if ($type === MAP_ELF) {
            return $this->num_elfs > 0;
        }
        if ($type === MAP_GOBLIN) {
            return $this->num_goblins > 0;
        }
        return $this->num_elfs > 0 && $this->num_goblins > 0;
    }

    public function removeCombatant(Critter $critter) {
        $combatant = $this->combatants[$critter->critterId()] ?? null;
        if (!isset($combatant) || !$combatant->equals($critter)) {
            return;
        }

        $loc = $combatant->mapLocation();
        unset($this->combatants[$combatant->critterId()]);
        if ($combatant->type() === MAP_ELF) {
            $this->num_elfs--;
        }
        if ($combatant->type() === MAP_GOBLIN) {
            return $this->num_goblins--;
        }
        $this->setItemAt($loc->x, $loc->y, MAP_FLOOR);
    }

    public function doTurn() {
        $interrupted = false;
        foreach (array_keys($this->combatants) as $critter_id) {
            // get critter
            $critter = $this->combatants[$critter_id] ?? null;
            if (!$critter) {
                break;
            }

            $this->last_combatant = $critter_id;

            if (!$this->hasCombatants()) {
                $interrupted = true;
                break;
            }
            // take turn
            $this->turns++;
            $critter->move();
            $critter->attack();
            $this->updateMap();
        }

        if (!$interrupted) {
            $this->rounds++;
        }

        if ($this->hasCombatants()) {
            // sort combatants
            uasort(
                $this->combatants,
                function($a, $b) {
                    [$xa, $ya] = $a->coords();
                    [$xb, $yb] = $b->coords();
                    //echo "{$a->critterId()}:[$xa, $ya] {$b->critterId()}:[$xb, $yb]\n";
                    $ydiff = $ya <=> $yb;
                    if ($ydiff === 0) {
                        return $xa <=> $xb;
                    }
                    return $ydiff;
                }
            );

        }
            //$this->printDebug();
    }

    public function itemAt($x, $y): BattleMapLocation {
        if ($x < 0 || $x > $this->max_x - 1 || $y < 0 || $y > $this->max_y + 1) {
            return new BattleMapLocation($x, $y, MAP_WALL);
        }
        return new BattleMapLocation($x, $y, $this->cave_map[$this->xytoi($x, $y)]);
    }

    public function combatantAt($x, $y, $type = null): ?Critter {
        $item = $this->itemAt($x, $y);
        if ($type && $item->type !== $type) {
            return null;
        }

        if ($item->type === MAP_ELF || $item->type === MAP_GOBLIN) {
            foreach ($this->combatants as $critter) {
                if ($critter->coords() === [$x, $y]) {
                    return $critter;
                }
            }
        }
        echo __METHOD__ . "\n";
        print_r($item);

        return null;
    }

    protected function setItemAt($x, $y, $type) {
        if ($x < 0 || $x > $this->max_x - 1 || $y < 0 || $y > $this->max_y + 1) {
            return false;
        }
        $this->cave_map[$this->xytoi($x, $y)] = $type;
        return true;
    }

    public function updateMap() {
        // pick up all the pieces
        $this->cave_map = strtr($this->cave_map, [MAP_ELF => MAP_FLOOR, MAP_GOBLIN => MAP_FLOOR]);
        // put them all back down
        foreach ($this->combatants as $critter) {
            [$x, $y] = $critter->coords();
            $this->setItemAt($x, $y, $critter->type());
        }
    }

    public function printMap() {
        $out = "Turn: {$this->turns} rounds: {$this->rounds} last turn by {$this->last_combatant}\n";
        $out .= implode("\n", str_split($this->cave_map, $this->max_x)) . "\n";
        return $out;
    }

    public function printDebug() {
        echo $this->printMap();
        print_r($this->combatants);
    }

    public function adjacent($x, $y) {
        return [
            // this order is to make the choosing faster
            DIR_U   => $this->itemAt($x, $y - 1),
            DIR_L   => $this->itemAt($x - 1, $y),
            DIR_R   => $this->itemAt($x + 1, $y),
            DIR_D   => $this->itemAt($x, $y + 1),
        ];
    }

    public function adjacentOfType($x, $y, $type) {
        return array_filter(
            $this->adjacent($x, $y),
            function($adj) use ($type) {
                return $adj->type === $type;
            }
        );
    }

    public function adjacentEnemies($x, $y, $type) {
        return array_filter([
            // this order is to make the choosing faster
            DIR_U   => $this->combatantAt($x, $y - 1, $type),
            DIR_L   => $this->combatantAt($x - 1, $y, $type),
            DIR_R   => $this->combatantAt($x + 1, $y, $type),
            DIR_D   => $this->combatantAt($x, $y + 1, $type),
        ]);
    }

    public function bestNextStep(BattleMapLocation $from, string $targetType): ?BattleMapLocation {
        [$cx, $cy] = $from->coords();
        $adj_floors = $this->adjacentOfType($cx, $cy, MAP_FLOOR);
        if (!$adj_floors) {
            return null;
        }

        $dests = array_filter(array_map(
            function($loc) use ($targetType) {
                return $this->bestDest($loc, $targetType);
            },
            $adj_floors
        ));

        if (!$dests) {
            return null;
        }

        if (count($dests) > 1) {
            usort(
                $dests,
                function($a, $b) {
                    $dist_diff = $a->dist <=> $b->dist;
                    if ($dist_diff !== 0) {
                        return $dist_diff;
                    }
                    // at the tail
                    $ydiff = $a->location->y <=> $b->location->y;
                    if ($ydiff !== 0) {
                        return $ydiff;
                    }
                    $xdiff = $a->location->x <=> $b->location->x;
                    if ($xdiff !== 0) {
                        return $xdiff;
                    }
                    // at the head
                    $a = $a->headNode(); $b = $b->headNode();
                    $ydiff = $a->location->y <=> $b->location->y;
                    if ($ydiff !== 0) {
                        return $ydiff;
                    }
                    $xdiff = $a->location->x <=> $b->location->x;
                    if ($xdiff !== 0) {
                        return $xdiff;
                    }
                    return 0;
                }
            );
        }

        return (array_shift($dests))->headNode()->location;
    }

    public function bestDest(BattleMapLocation $from, string $targetType): ?BattleMapPathNode {
        // start the map with our origin
        $mapped = [
            $from->y => [
                $from->x => new BattleMapPathNode($from),
            ],
        ];

        $coords_set = [$from->coords()];
        $current = null;
        $found = false;
        $dist = 1;
        $found_dist = PHP_INT_MAX;
        $max = strlen($this->cave_map);
        while (count($coords_set) && $max--) {
            $next = [];
            foreach ($coords_set as $coords) {
                [$cx, $cy] = $coords;

                $current = $mapped[$cy][$cx];

                // if enemy adjacent, stop here
                $adj_enemies = $this->adjacentOfType($cx, $cy, $targetType);
                if ($adj_enemies) {
                    $found = true;
                    break 2;
                }

                // no open spaces, skip along
                $adj_floors = $this->adjacentOfType($cx, $cy, MAP_FLOOR);
                if (!$adj_floors) {
                    continue;
                }

                // map all the adjacent floors
                foreach ($adj_floors as $floor) {
                    // skip visited nodes
                    if (isset($mapped[$floor->y][$floor->x])) {
                        continue;
                    }
                    // add unvisited ones
                    if (!isset($mapped[$floor->y])) {
                        $mapped[$floor->y] = [];
                    }
                    $mapped[$floor->y][$floor->x] = new BattleMapPathNode($floor, $current, $dist);
                    $next[] = $floor->coords();
                }
            }

            $dist++;
            $coords_set = $next;
            uasort(
                $coords_set,
                function($a, $b) {
                    [$xa, $ya] = $a;
                    [$xb, $yb] = $b;
                    $ydiff = $ya <=> $yb;
                    if ($ydiff === 0) {
                        return $xa <=> $xb;
                    }
                    return $ydiff;
                }
            );
        }

        if (false) {
            $out = $this->cave_map;
            foreach ($mapped as $y => $row){
                foreach ($row as $x => $node) {
                    $out[ $this->xytoi($x, $y) ] = dechex($node->dist);
                }
            }
            echo "mapped path:\n" . implode("\n", str_split($out, $this->max_x)) . "\n";
        }
        if (!$found) {
            return null;
        }

        return $current;
    }

    public function moveItem(BattleMapLocation $from, BattleMapLocation $to) {
        if (!($from->type === MAP_ELF || $from->type === MAP_GOBLIN)) {
            throw new RuntimeException("Can't move this kind of item from:{$from} to:{$to}!");
        }
        if ($to->type !== MAP_FLOOR) {
            throw new RuntimeException("Can't move into this space from:{$from} to:{$to}!");
        }
        $from_type = $from->type;
        $to_type = $to->type;
        $this->setItemAt($from->x, $from->y, $to_type);
        $this->setItemAt($to->x, $to->y, $from_type);
    }
}

class BattleMapLocation {
    public $x, $y, $type;
    public function __construct($x, $y, $type) {
        $this->x = $x;
        $this->y = $y;
        $this->type = $type;
    }

    public function coords() {
        return [$this->x, $this->y];
    }

    public function __toString() {
        return "BML({$this->x},{$this->y},{$this->type})";
    }
}

class BattleMapPathNode {
    public $location, $prev, $dist;
    public function __construct(BattleMapLocation $location, BattleMapPathNode $prev = null, int $dist = 0) {
        $this->location = $location;
        $this->prev = $prev;
        $this->dist = $dist;
    }

    public function headNode() {
        $head = $this;
        while ($head->dist > 0) {
            $head = $head->prev;
        }
        return $head;
    }
}
