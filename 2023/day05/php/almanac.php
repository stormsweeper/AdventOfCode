<?php

$almanac = trim(file_get_contents($argv[1]));
[
    $seeds, $seed2soil, $soil2fertilizer, $fertilizer2water, $water2light, $light2temp, $temp2hum, $hum2loc
] = explode("\n\n", $almanac);

function parse_nums(string $nums): array {
    return array_map('intval', explode(' ', $nums));
}

function parse_map_rules(string $map): array {
    $map = explode("\n", $map);
    array_shift($map);
    return array_map('parse_nums', $map);
}

function map2dest(int $src, array $map): int {
    foreach ($map as [$dest_start, $src_start, $len]) {
        $diff = $src - $src_start;
        if ($diff >= 0 && $diff < $len) return $dest_start + $diff;
    }
    return $src;
}

[
    $seed2soil, $soil2fertilizer, $fertilizer2water, $water2light, $light2temp, $temp2hum, $hum2loc
] = array_map('parse_map_rules', [
    $seed2soil, $soil2fertilizer, $fertilizer2water, $water2light, $light2temp, $temp2hum, $hum2loc
]);

$seeds = parse_nums(substr($seeds, 7));

$min_loc = PHP_INT_MAX;

foreach ($seeds as $seed) {
    $loc = map2dest(
        map2dest(
            map2dest(
                map2dest(
                    map2dest(
                        map2dest(
                            map2dest(
                                $seed,
                                $seed2soil
                            ),
                            $soil2fertilizer
                        ),
                        $fertilizer2water
                    ),
                    $water2light
                ),
                $light2temp
            ),
            $temp2hum
        ),
        $hum2loc
    );
    $min_loc = min($min_loc, $loc);
}

echo $min_loc;