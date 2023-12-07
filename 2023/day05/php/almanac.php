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

echo "p1: {$min_loc}\n";

// create starting ranges for seeds

$seed_ranges = [
    [$seeds[0], $seeds[0], $seeds[1]],
    [$seeds[2], $seeds[2], $seeds[3]],
];

// combine start range with map ranges one map mat a time, for each one:
// (first pass srcs is seeds, dests is soil, etc)
function flatten_ranges(array $srcs, array $dests): array {
    $mapped_ranges = [];
    while ($srcs) {
        $src_range = array_pop($srcs);
        foreach ($dests as $dest_range) {
            [$mapped, $unmapped] = split_ranges($src_range, $dest_range);
            if ($mapped) $mapped_ranges = array_merge($mapped_ranges, $mapped);
            if ($unmapped) $srcs = array_merge($srcs, $unmapped);
        }
    }
    return $mapped_ranges;
}

// calculate intersecting range, split src into multiple ranges
function split_ranges(array $src_range, array $dest_range): array {
    // use the dest of the src
    [$src_dest_start, $src_src_start, $src_len] = $src_range;
    $src_dest_end = $src_dest_start + $src_len - 1;
    $src_offset = $src_dest_start - $src_src_start;

    // use the src of the dest
    [$dest_dest_start, $dest_src_start, $dest_len] = $dest_range;
    $dest_src_end = $dest_src_start + $dest_len - 1;
    $dest_offset = $dest_dest_start - $dest_src_start;

    $mapped = $unmapped = [];

    // non-intersecting
    if ($dest_src_end < $src_dest_start || $dest_src_start > $src_dest_end) {
        $unmapped = [$src_range];
        // echo "no overlap\n";
    }
    else {
        $mapped_src_start = max($src_dest_start, $dest_src_start);
        $left_len = $mapped_src_start - $src_dest_start;
        $mapped_src_end = min($src_dest_end, $dest_src_end);
        $right_len = $src_dest_end - $mapped_src_end;
        $mapped_len = $mapped_src_end - $mapped_src_start + 1;

        $mapped = [$mapped_src_start + $dest_offset, $src_src_start + $left_len, $mapped_len];
        if ($left_len)  $unmapped[] = [$src_dest_start, $src_src_start, $left_len];
        if ($right_len) $unmapped[] = [$src_dest_start + $left_len + $mapped_len, $src_src_start + $left_len + $mapped_len, $right_len];
        // echo "overlap {$left_len} {$mapped_len} {$right_len}\n";
    }

    return [$mapped, $unmapped];
}

(function(){
    // non-intersecting
    $src_range = [1,21,5];
    $dest_range = [16,6,5];
    [$mapped, $unmapped] = split_ranges($src_range, $dest_range);
    // print_r($mapped);
    // print_r($unmapped);
    assert($mapped === []);
    assert($unmapped === [[1,21,5]]);

    // subset
    $src_range = [1,21,5];
    $dest_range = [12,2,3];
    [$mapped, $unmapped] = split_ranges($src_range, $dest_range);
    // print_r($mapped);
    // print_r($unmapped);
    assert($mapped === [12,22,3]);
    assert($unmapped === [[1,21,1],[5,25,1]]);

    // mapped overlaps to left
    $src_range = [5,25,5];
    $dest_range = [11,1,5];
    [$mapped, $unmapped] = split_ranges($src_range, $dest_range);
    assert($mapped === [15,25,1]);
    assert($unmapped === [[6,26,4]]);

    // mapped overlaps to right
    $src_range = [1,21,5];
    $dest_range = [13,3,5];
    [$mapped, $unmapped] = split_ranges($src_range, $dest_range);
    // print_r($mapped);
    // print_r($unmapped);
    assert($mapped === [13,23,3]);
    assert($unmapped === [[1,21,2]]);
})();

print_r(flatten_ranges($seed2soil, $soil2fertilizer));