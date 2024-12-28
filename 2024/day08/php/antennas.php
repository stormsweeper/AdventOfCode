<?php

$map = explode("\n", trim(file_get_contents($argv[1])));
$map_height = count($map);
$map_width = strlen($map[0]);

$antennas = $antinodes = $all_antinodes = [];
for ($y = 0; $y < $map_height; $y++) {
    for ($x = 0; $x < $map_width; $x++) {
        $scan = $map[$y][$x];
        if ($scan === '.') continue;
        if (!isset($antennas[$scan])) {
            $antennas[$scan] = [];
        }
        $antennas[$scan][] = [$x,$y];
    }
}

function in_map(array $coords) {
    global $map_width, $map_height;
    [$x, $y] = $coords;
    return $x >= 0 && $x < $map_width && $y >= 0 && $y < $map_height;
}

function dist(array $ant_a, array $ant_b): array {
    return [$ant_b[0] - $ant_a[0], $ant_b[1] - $ant_a[1]];
}

function antinodes(array $ant_a, array $ant_b): array {
    $antis = [];

    $dist = [$ant_b[0] - $ant_a[0], $ant_b[1] - $ant_a[1]];

    $anti_a = [$ant_a[0] - $dist[0], $ant_a[1] - $dist[1]];
    if (in_map($anti_a)) $antis[] = $anti_a;

    $anti_b = [$ant_b[0] + $dist[0], $ant_b[1] + $dist[1]];
    if (in_map($anti_b)) $antis[] = $anti_b;

    return $antis;
}

function all_antinodes(array $ant_a, array $ant_b): array {
    $antis = [];

    $dist = [$ant_b[0] - $ant_a[0], $ant_b[1] - $ant_a[1]];

    $anti_a = $ant_a;
    do {
        if (in_map($anti_a)) $antis[] = $anti_a;
        $anti_a = [$anti_a[0] - $dist[0], $anti_a[1] - $dist[1]];
    } while (in_map($anti_a));

    $anti_b = $ant_b;
    do {
        if (in_map($anti_b)) $antis[] = $anti_b;
        $anti_b = [$anti_b[0] + $dist[0], $anti_b[1] + $dist[1]];
    } while (in_map($anti_b));

    return $antis;
}

foreach ($antennas as $freq => $ants) {
    $ant_num = count($ants);
    if ($ant_num < 2) return;

    for ($a = 0; $a < $ant_num - 1; $a++) {
        for ($b = $a + 1; $b < $ant_num; $b++) {
            foreach (antinodes($ants[$a], $ants[$b]) as $anti) {
                $antinodes["{$anti[0]},{$anti[1]}"] = true;
            }
            foreach (all_antinodes($ants[$a], $ants[$b]) as $anti) {
                $all_antinodes["{$anti[0]},{$anti[1]}"] = true;
            }
        }
    }
}

$p1 = count($antinodes);
echo "p1: {$p1}\n";
$p2 = count($all_antinodes);
echo "p2: {$p2}\n";
