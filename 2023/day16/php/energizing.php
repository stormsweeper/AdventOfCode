<?php

$cavern = explode("\n", trim(file_get_contents($argv[1])));
$height = count($cavern);
$width = strlen($cavern[0]);

class Photon {
    static function create(int $x, int $y, string $dir): ?Photon {
        global $width, $height;
        if ($x < 0 || $x >= $width || $y < 0 || $y >= $height) return null;
        return new self($x, $y, $dir);
    }
    function __construct(public int $x, public int $y, public string $dir) {}
    function hashkey(): string { return "{$this->x};{$this->y};{$this->dir}"; }
    function poskey(): string { return "{$this->x},{$this->y}"; }

    function up():    ?Photon { return self::create($this->x, $this->y - 1, 'U'); }
    function down():  ?Photon { return self::create($this->x, $this->y + 1, 'D'); }
    function left():  ?Photon { return self::create($this->x - 1, $this->y, 'L'); }
    function right(): ?Photon { return self::create($this->x + 1, $this->y, 'R'); }

    function next(): array {
        $feature = feature_at($this->x, $this->y);
        $next = [];
        if ($this->dir === 'U') {
            switch ($feature) {
                case '/': $next[] = $this->right(); break;
                case '\\': $next[] = $this->left(); break;
                case '-': $next[] = $this->left(); $next[] = $this->right(); break;
                default: $next[] = $this->up();
            }
        }
        if ($this->dir === 'D') {
            switch ($feature) {
                case '/': $next[] = $this->left(); break;
                case '\\': $next[] = $this->right(); break;
                case '-': $next[] = $this->left(); $next[] = $this->right(); break;
                default: $next[] = $this->down();
            }
        }
        if ($this->dir === 'L') {
            switch ($feature) {
                case '/': $next[] = $this->down(); break;
                case '\\': $next[] = $this->up(); break;
                case '|': $next[] = $this->up(); $next[] = $this->down(); break;
                default: $next[] = $this->left();
            }
        }
        if ($this->dir === 'R') {
            switch ($feature) {
                case '/': $next[] = $this->up(); break;
                case '\\': $next[] = $this->down(); break;
                case '|': $next[] = $this->up(); $next[] = $this->down(); break;
                default: $next[] = $this->right();
            }
        }
        return array_filter($next);
    }
}

function feature_at(int $x, int $y): string {
    global $width, $height, $cavern;
    if ($x < 0 || $x >= $width || $y < 0 || $y >= $height) return '';
    return $cavern[$y][$x];
}

$energized = [];
$start = Photon::create(0, 0, 'R');
$consider = [$start->hashkey() => $start];
$visited = [];
while ($consider) {
    $photon = array_pop($consider);
    $energized[$photon->poskey()] = $energized[$photon->poskey()]??0 + 1;
    foreach ($photon->next() as $n) {
        if (!isset($visited[$n->hashkey()])) $consider[$n->hashkey()] = $n;
    }
    $visited[$n->hashkey()] = $n;
}

echo count($energized);
