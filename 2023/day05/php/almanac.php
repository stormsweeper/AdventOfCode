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
    $map = array_map('parse_nums', $map);
    usort($map, 'sort_ranges_by_start');
    return $map;
}

function map2dest(int $src, array $map): int {
    foreach ($map as [$dest_start, $src_start, $len]) {
        $diff = $src - $src_start;
        if ($diff >= 0 && $diff < $len) return $dest_start + $diff;
    }
    return $src;
}

function sort_ranges_by_start($a, $b) { return $a[1] <=> $b[1]; }

function best_loc($seed): int {
    global $seed2soil, $soil2fertilizer, $fertilizer2water, $water2light, $light2temp, $temp2hum, $hum2loc;
    return map2dest(
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
}


[
    $seed2soil, $soil2fertilizer, $fertilizer2water, $water2light, $light2temp, $temp2hum, $hum2loc
] = array_map('parse_map_rules', [
    $seed2soil, $soil2fertilizer, $fertilizer2water, $water2light, $light2temp, $temp2hum, $hum2loc
]);

$seeds = parse_nums(substr($seeds, 7));

$min_loc = PHP_INT_MAX;

foreach ($seeds as $seed) {
    $loc = best_loc($seed);
    $min_loc = min($min_loc, $loc);
}


echo "p1: {$min_loc}\n";

// create starting ranges for seeds

$seed_ranges = [
    [$seeds[0], $seeds[0], $seeds[1]],
    [$seeds[2], $seeds[2], $seeds[3]],
];

usort($seed_ranges, 'sort_ranges_by_start');


$min_loc = PHP_INT_MAX;

foreach ($seed_ranges as [, $seed, $len]) {
    for ($i = 0; $i < $len; $i++) {
        $loc = best_loc($seed + $i);
        $min_loc = min($min_loc, $loc);
    }
}


echo "p2: {$min_loc}\n";