<?php

class ArmyGroup {
    const TYPE_IMMUNE_SYSTEM = "immuneSystem";
    const TYPE_INFECTION = "infection";
    const PARSE_REGEXP = '/^(?<size>\d+) units each with (?<hit_points>\d+) hit points (?:\((?<specials>[^)]+)\) )?with an attack that does (?<damage>\d+) (?<dtype>\w+) damage at initiative (?<initiative>\d+)$/';

    public static $group_counts = [
        self::TYPE_IMMUNE_SYSTEM => 0,
        self::TYPE_INFECTION => 0,
    ];

    public static $immune_boost = 0;

    public static function hasGroupsOfType($gtype) {
        return self::$group_counts[$gtype] > 0;
    }

    public static function battleWagesOn() {
        return self::hasGroupsOfType(self::TYPE_IMMUNE_SYSTEM) && self::hasGroupsOfType(self::TYPE_INFECTION);
    }

    public function __construct($gtype, $line) {
        preg_match(self::PARSE_REGEXP, trim($line), $m);
        $this->id = ++self::$group_counts[$gtype];
        $this->key = $gtype . $this->id;
        $this->gtype = $gtype;
        $this->size = intval($m['size']);
        $this->hit_points = intval($m['hit_points']);
        $this->damage = intval($m['damage']);
        $this->dtype = $m['dtype'];
        $this->initiative = intval($m['initiative']);
        [$this->weak, $this->immune] = $this->parseSpecials($m['specials']);
    }

    private function parseSpecials($text) {
        $weak = $immune = [];
        $specials = explode('; ', $text);
        foreach ($specials as $quality) {
            [$effect, $types] = array_pad(explode(' to ', $quality), 2, '');
            if (!$effect) {
                continue;
            }
            $types = explode(', ', $types);
            if ($effect === 'immune') {
                $immune = array_merge($immune, $types);
            } else {
                $weak = array_merge($weak, $types);
            }
        }
        return [$weak, $immune];
    }

    public function enemyType() { return $this->gtype === self::TYPE_IMMUNE_SYSTEM ? self::TYPE_INFECTION : self::TYPE_IMMUNE_SYSTEM; }

    public function effectivePower() {
        assert($this->isAlive(), "Shouldn't calculate EP if dead");
        if ($this->gtype === self::TYPE_IMMUNE_SYSTEM) {
            return $this->size * ($this->damage + self::$immune_boost);
        }
        return $this->size * $this->damage;
    }

    public function isAlive() { return $this->size > 0; }

    protected function adjustReceivedDamage($damage, $dtype) {
        assert($this->isAlive(), "Shouldn't need to calc damage if not alive");
        if (in_array($dtype, $this->immune)) {
            return 0;
        }

        if (in_array($dtype, $this->weak)) {
            return 2 * $damage;
        }

        return $damage;
    }

    public function selectTarget($groups) {
        assert($this->isAlive(), "Can't select target if not alive");
        if (!$this->isAlive()) {
            return false;
        }

        $enemies = array_filter(
            $groups,
            function(ArmyGroup $g) {
                return  $g->isAlive() && // has units left
                        $g->gtype === $this->enemyType() && // of the enemy type
                        !isset($g->attacker) && // is not already selected
                        $g->adjustReceivedDamage(1, $this->dtype) > 0; // could take damage
            }
        );
        //echo "possible: " . json_encode(array_keys($enemies)) . "\n";

        if (empty($enemies)) {
            return false;
        }

        $enemy = null;
        if (count($enemies) === 1) {
            $enemy = array_pop($enemies);
        }
        else {
            uasort(
                $enemies,
                function(ArmyGroup $a, ArmyGroup $b) {
                    // take highest received damage
                    $ddiff = $b->adjustReceivedDamage($this->effectivePower(), $this->dtype) <=> $a->adjustReceivedDamage($this->effectivePower(), $this->dtype);
                    if ($ddiff !== 0) {
                        //echo "using damage\n";
                        return $ddiff;
                    }
    
                    // take higher power 
                    $epdiff = $b->effectivePower() <=> $a->effectivePower();
                    if ($epdiff !== 0) {
                        //echo "using power\n";
                        return $epdiff;
                    }
    
                    // take highest init
                        //echo "using init\n";
                    $idiff = $b->initiative <=> $a->initiative;
                    if ($idiff !== 0) {
                        //echo "using power\n";
                        return $idiff;
                    }

                    return $a->id <=> $b->id;
                }
            );
            //echo "ranked: " . json_encode(array_keys($enemies)) . "\n";
            $enemy = array_shift($enemies);
        }

        if ($enemy) {
            echo "{$this->key} selected defender {$enemy->key}\n";
            $enemy->attacker = $this;
            $this->target = $enemy;
            return true;
        }
        return false;
    }

    public function mayAttack() {
        return  $this->isAlive() &&
                isset($this->target) && $this->target->isAlive();
    }

    public function attackTarget() {
        assert($this->isAlive(), "Shouldn't attack if not alive");
        assert(isset($this->target) && $this->target->isAlive(), "Shouldn't attack if it has no target");
        if (!$this->mayAttack()) {
            return false;
        }

        $target = $this->target;
        $this->target = null;

        return $target->receiveDamage();
    }

    protected function receiveDamage() {
        assert($this->isAlive(), "Shouldn't be a target if not alive");
        assert(isset($this->attacker), "Shouldn't be a target if no attacker");
        $attacker = $this->attacker;
        $this->attacker = null;
        if (!isset($attacker) || !$this->isAlive()) {
            return 0;
        }

        $damage = $this->adjustReceivedDamage($attacker->effectivePower(), $attacker->dtype);
        assert($damage > 0, "Should have not been a target if it was immune");
        if ($damage < 1) {
            return 0;
        }

        $units_lost = min($this->size, floor($damage / $this->hit_points));
        $this->size -= $units_lost;
        if (!$this->isAlive()) {
            self::$group_counts[$this->gtype]--;
        }
        return $units_lost;
    }
}


function getSelectionOrder($groups) {
    $groups = array_filter(
        $groups,
        function($g) { return $g->isAlive(); }
    );
    uasort(
        $groups,
        function (ArmyGroup $a, ArmyGroup $b) {
            // take higher power 
            $epdiff = $b->effectivePower() <=> $a->effectivePower();
            if ($epdiff !== 0) {
                return $epdiff;
            }

            // take highest init
            $b->initiative <=> $a->initiative;
        }
    );
    return array_keys($groups);
}

$initiatives = [];
$groups = [];
$gtype = null;

foreach (file($argv[1]) as $line) {
    if (strpos($line, 'Immune System:') !== false) {
        $gtype = ArmyGroup::TYPE_IMMUNE_SYSTEM;
    }
    elseif (strpos($line, 'Infection:') !== false) {
        $gtype = ArmyGroup::TYPE_INFECTION;
    }
    elseif (strlen($line) > 1) {
        $group = new ArmyGroup($gtype, $line);
        $groups[$group->key] = $group;
        $initiatives[$group->key] = $group->initiative;
    }
}

ArmyGroup::$immune_boost = intval($argv[2] ?? 0);

arsort($initiatives);

$rounds = 0;
$max_rounds = 10000;

while (ArmyGroup::battleWagesOn() && $rounds < $max_rounds) {
    $rounds++;
    if ($rounds === 499) {
        print_r($groups['immuneSystem1']);
        print_r($groups['infection8']);
        print_r($groups['infection9']);
        exit;
    }
    echo "\nROUND {$rounds}\n";
    // sort by EP,init descending
    $selectors = getSelectionOrder($groups);

    // select targets
    $selected_any = false;
    foreach ($selectors as $key) {
        $group = $groups[$key];
        $did_select = $group->selectTarget($groups);
        $selected_any = $selected_any || $did_select;
    }
    if (!$selected_any) {
        echo "stalemate! (no selections)";
        exit;
    }

    // attack targets
    $kill_count = 0;
    foreach ($initiatives as $key => $init) {
        $group = $groups[$key] ?? null;
        if (isset($group) && $group->mayAttack()) {
            $target = $group->target;
            $killed = $group->attackTarget();
            $kill_count += $killed;
            echo "{$group->key} attacked {$target->key} killing {$killed} units\n";
        }
    }
    if ($kill_count < 1) {
        echo "stalemate! (no kills)";
        exit;
    }

    // clear targets just in case
    foreach ($groups as $group) {
        $group->target = null;
        $group->attacker = null;
    }

}

$remaining = array_sum(
    array_map(
        function($g) {
            return $g->size;
        },
        $groups
    )
);

$immune_count = ArmyGroup::$group_counts[ArmyGroup::TYPE_IMMUNE_SYSTEM];
$infection_count = ArmyGroup::$group_counts[ArmyGroup::TYPE_INFECTION];
echo "Immune System active: {$immune_count}\n";
echo "Infections active: {$infection_count}\n";
echo "Total Remaining: {$remaining}\n";