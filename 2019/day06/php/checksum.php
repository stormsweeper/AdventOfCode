<?php

$input = trim(file_get_contents($argv[1]));
$input = explode("\n", $input);

$thingA = $argv[2] ?? null;
$thingB = $argv[3] ?? null;

class Thing {
    static $things = [];
    public $name, $center = null, $depth = 0, $satts = [];

    function __construct(string $name) {
        $this->name = $name;
    }

    public static function getThing(string $name): Thing {
        if (!isset(self::$things[$name])) {
            self::$things[$name] = new Thing($name);
        }
        return self::$things[$name];
    }

    public function addSatt(Thing $satt) {
        if (isset($satt->center)) {
            throw new RuntimeException(
                "Can't set {$satt->name} in orbit around {$this->name}, it already orbits {$satt->center->name}"
            );
        }
        $satt->center = $this;
        $this->satts[$satt->name] = $satt;
        $satt->adjustOrbitDepths();
    }

    private function adjustOrbitDepths() {
        $this->depth = $this->center->depth + 1;
        foreach ($this->satts as $satt) {
            $satt->adjustOrbitDepths();
        }
    }

    function orbitPaths(): array {
        $paths = [];
        $thing = $this;
        while (isset($thing->center)) {
            $paths[] = $thing->center->name;
            $thing = $thing->center;
        }
        return $paths;
    }

    function commonAncestor(Thing $other): ?Thing {
        $shared_path = array_intersect($this->orbitPaths(), $other->orbitPaths());
        if (empty($shared_path)) {
            return null;
        }
        return self::getThing(array_shift($shared_path));
    }

    function transfersRequired(Thing $other): int {
        if ($this->center->name === $other->center->name) {
            return 0;
        }

        $ancestor = $this->commonAncestor($other);
        if (!$ancestor) {
            return -1;
        }
        return $this->center->depth + $other->center->depth - 2*$ancestor->depth;
    }
}

foreach ($input as $line) {
    list($center, $satt) = explode(')', $line);
    Thing::getThing($center)->addSatt(Thing::getThing($satt));
}

$checksum = 0;
foreach (Thing::$things as $thing) {
    $checksum += $thing->depth;
}

echo "Checksum: {$checksum}\n";

if (isset($thingA) && isset($thingB)) {
    $thingA = Thing::getThing($thingA);
    $thingB = Thing::getThing($thingB);
    $transfers = $thingA->transfersRequired( $thingB );
    echo "Orbital transfers: {$transfers}\n";
}
