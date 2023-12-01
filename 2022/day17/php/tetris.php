<?php

$vent_pattern = trim(file_get_contents($argv[1]));
$max_blocks = intval($argv[2]);
$width = 7;
$chamber = array_fill(0, $width, 0);

class FallingBit {
    function __construct(public int $x, public int $y) {}
    static function sort(FallingBit $a, FallingBit $b) {
        $by_y = $a->y <=> $b->y;
        if ($by_y === 0) return $a->x <=> $b->x;
        return $by_y;
    }
}

abstract class FallingShape {
    function __construct(private array $bits) {
        usort($this->bits, 'FallingBit::sort');
    }

    function canFall(array $chamber): bool {
        return false;
    }
    function fall(array $chamber): void {
        if (!$this->canFall(array $chamber)) return;
    }

    function canGo(array $chamber, string $dir): bool {
        return false;
    }
    function go(array $chamber, string $dir): void {
        if (!$this->canGo($chamber, $dir)) return;
    }
}

class FallingHorzLine extends FallingShape {
    function __construct(int $x, int $y) {
        parent::__construct([
            new FallingBit($x, $y),
            new FallingBit($x+1, $y),
            new FallingBit($x+2, $y),
            new FallingBit($x+3, $y),
        ]);
    }
}

print_r(new FallingHorzLine(2, 3));