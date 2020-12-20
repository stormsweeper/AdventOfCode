<?php

class MIBTile {
    const TILE_SIZE = 10;

    public $tile_id = 0;
    public $grid = [];
    public $edges = [];
    public $compatible_to = [];
    public $compatible_on = [
        'top' => [],
        'bottom' => [],
        'left' => [],
        'right' => [],
    ];

    function __construct(string $tilestr) {
        list($header, $grid) = explode("\n", $tilestr, 2);
        $this->tile_id = intval(substr($header, 5, -1));
        $this->grid = MIBTile::parse_grid($grid);
        $this->edges = MIBTileEdge::find_edges($this);
    }

    function valueAt(int $x, int $y): int {
        return $this->grid[$this->pos2key($x, $y)] ?? 0;
    }

    function calc_compat(MIBTile $other): void {
        foreach ($this->edges as $t_side => $t_edge) {
            foreach ($other->edges as $o_side => $o_edge) {
                if ($t_edge->compatible_with($o_edge)) {
                    if (!isset($this->compatible_on[$t_side][$other->tile_id])) $this->compatible_on[$t_side][$other->tile_id] = [];
                    $this->compatible_on[$t_side][$other->tile_id][] = $o_side;
                    if (!isset($other->compatible_on[$o_side][$this->tile_id])) $other->compatible_on[$o_side][$this->tile_id] = [];
                    $other->compatible_on[$o_side][$this->tile_id][] = $o_side;
                }
            }
        }
    }

    function num_compatible_sides(): int {
        $num = 0;
        if (!empty($this->compatible_on['top'])) $num++;
        if (!empty($this->compatible_on['bottom'])) $num++;
        if (!empty($this->compatible_on['left'])) $num++;
        if (!empty($this->compatible_on['right'])) $num++;
        return $num;
    }

    static function parse_tiles(string $tiles): array {
        $tiles = explode("\n\n", trim($tiles));
        $parsed = [];
        foreach ($tiles as $t) {
            $tile = new MIBTile($t);
            $parsed[$tile->tile_id] = $tile;
        }
        return $parsed;
    }

    static function parse_grid(string $map): array {
        $grid = [];
        $lines = explode("\n", trim($map));
        $size = count($lines);
        foreach ($lines as $y => $line) {
            for ($x = 0; $x < MIBTile::TILE_SIZE; $x++) {
                if ($line[$x] === '#') {
                    $grid[MIBTile::pos2key($x, $y)] = 1;
                }
            }
        }
        return $grid;
    }

    static function pos2key(int $x, int $y): string {
        return sprintf('%03d,%03d', $x, $y);
    }

    static function key2pos(string $key): array {
        return sscanf($key, '%03d,%03d');
    }
}

class MIBTileEdge {
    public $values = '';
    public $normalized = '';
    public $reversed = false;
    public $bidirectional = false;

    function __construct(array $values) {
        $this->values = implode('', $values);
        $reverse = strrev($this->values);
        if ($this->values === $reverse) {
            $this->bidirectional = true;
            $this->normalized = $this->values;
        } else {
            $this->normalized = min($this->values, $reverse);
            $this->reversed = $this->values !== $this->normalized;
        }
    }

    static function find_edges(MIBTile $tile): array {
        $top = $right = $bottom = $left = [];
        for ($i = 0; $i < MIBTile::TILE_SIZE; $i++) {
            $top[] = $tile->valueAt($i, 0);
            $bottom[] = $tile->valueAt($i, MIBTile::TILE_SIZE - 1);
            $left[] = $tile->valueAt(0, $i);
            $right[] = $tile->valueAt(MIBTile::TILE_SIZE - 1, $i);
        }

        return [
            'top' => new MIBTileEdge($top),
            'bottom' => new MIBTileEdge($bottom),
            'left' => new MIBTileEdge($left),
            'right' => new MIBTileEdge($right),
        ];
    }

    function compatible_with(MIBTileEdge $other) {
        return $this->normalized === $other->normalized;
    }
}