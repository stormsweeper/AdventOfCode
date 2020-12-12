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

function move(int $x, int $y, string $dir, int $dist): ?array {
    if ($dir === 'N') return [$x, $y + $dist];
    if ($dir === 'S') return [$x, $y - $dist];
    if ($dir === 'E') return [$x + $dist, $y];
    if ($dir === 'W') return [$x - $dist, $y];
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

echo "Part 1: {$p1}\n";

$ship_x = $ship_y = 0;
$waypoint_dx = 10; $waypoint_dy = 1;

function rotate_waypoint(int $x, int $y, string $dir, int $deg): ?array {
    $deg %= 360;
    if ($dir === 'L') {
        $deg *= -1;
    }
    $deg = ($deg + 360)%360;
    if ($deg === 0) return [$x, $y];
    if ($deg === 90) return [$y, $x * -1];
    if ($deg === 180) return [$x * -1, $y * -1];
    if ($deg === 270) return [$y * -1, $x];
}

foreach ($instructions as $inst) {
    $cmd = $inst[0];
    $dist = intval(substr($inst, 1));
    if ($cmd === 'L' || $cmd === 'R') {
        // rotate waypoint
        list($waypoint_dx, $waypoint_dy) = rotate_waypoint($waypoint_dx, $waypoint_dy, $cmd, $dist);
    } elseif ($cmd === 'F') {
        // move to waypoint x times
        $ship_x += $dist * $waypoint_dx;
        $ship_y += $dist * $waypoint_dy;
    } else {
        // move waypoint
        list($waypoint_dx, $waypoint_dy) = move($waypoint_dx, $waypoint_dy, $cmd, $dist);
    }
    #echo "Ship: {$ship_x},{$ship_y} Waypoint: {$waypoint_dx},{$waypoint_dy}\n";
}

$p2 = abs($ship_x) + abs($ship_y);

echo "Part 2: {$p2}\n";