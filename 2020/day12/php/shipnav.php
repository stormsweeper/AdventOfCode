<?php
$instructions = trim(file_get_contents($argv[1]));
$instructions = explode("\n", $instructions);

$x = $y = 0;
$facing = 'E';

function change_facing(string $facing, string $dir, int $deg): string {
    static $dirs = ['N' => 0, 'E' => 90, 'S' => 180, 'W' => 270];
    $deg %= 360;
    $c = $dirs[$facing];
    if ($dir === 'L') {
        $c -= $deg;
    } else {
        $c += $deg;
    }
    return array_search(($c + 360)%360, $dirs, true);
}

function move(int $x, int $y, string $dir, int $dist): array {
    if ($dir === 'N') return [$x, $y + $dist];
    if ($dir === 'S') return [$x, $y - $dist];
    if ($dir === 'E') return [$x + $dist, $y];
    if ($dir === 'W') return [$x - $dist, $y];
    return [];
}

foreach ($instructions as $inst) {
    $cmd = $inst[0];
    $dist = intval(substr($inst, 1));
    if ($cmd === 'L' || $cmd === 'R') {
        $facing = change_facing($facing, $cmd, $dist);
    } elseif ($cmd === 'F') {
        list($x, $y) = move($x, $y, $facing, $dist);
    } else {
        list($x, $y) = move($x, $y, $cmd, $dist);
    }
    #echo "{$x},{$y} Facing: {$facing}\n";
}

$p1 = abs($x) + abs($y);

echo "Part 1: {$p1}";