<?php

$entries = trim(file_get_contents($argv[1]));
$entries = explode("\n", $entries);

// p2
define('UNIQUE_LENGTHS', [2=>1, 3=>7, 4=>4, 7=>8]);
define('POSSIBLE_FOR_LENGTHS', [5=>[2,3,5], 6=>[0,6,9]]);

function sort_str(string $str): string {
    static $sorted = [];
    if (!isset($sorted[$str])) {
        $sorted[$str] = str_split($str);
        sort($sorted[$str]);
        $sorted[$str] = implode($sorted[$str]);
    }
    return $sorted[$str];
}

define('DIGIT_MASKS', [
    0 => [
        [5, 2]
    ],
    6 => [
        // if you mask out 1's segments, you are left with 5 on, the only for 6-segment numbers, etc
        [1, 5],
        [7, 4],
    ],
    9 => [
        [4, 2],
    ],
    2 => [
        [4, 3],
        [5, 2],
    ],
    3 => [
        [1, 3],
        [7, 2],
        [2, 1],
        [5, 1],
    ],
    5 => [
        [2, 2],
    ],

]);
function check_masks(string $sorted, array $scan): int {
    $len = strlen($sorted);
    foreach (POSSIBLE_FOR_LENGTHS[$len]??[] as $p) {
        foreach (DIGIT_MASKS[$p] as list($mask, $expected)) {
            if (empty($scan[$mask])) continue;
            $leftover = strtr($sorted, array_fill_keys(str_split($scan[$mask]), ''));
            if (strlen($leftover) === $expected) {
                return $p;
            }
        }
    }
    return -1;
}

function decode_code(array &$scan, string $code): int {
    $sorted = sort_str($code);

    $found = array_search($sorted, $scan);
    if ($found !== false) return $found;

    $len = strlen($code);
    // check known lens, if found return the int
    if (isset(UNIQUE_LENGTHS[$len])) {
        $val = UNIQUE_LENGTHS[$len];
        $scan[$val] = $sorted;
        return $val;
    }

    // check masks
    $val = check_masks($sorted, $scan);
    if ($val !== -1) {
        $scan[$val] = $sorted;
        return $val;
    }

    return -1;
}

function decode_line(string $line): int {
    $unscrambled = [];
    $scan = [];
    $scrambled = preg_split('/\W+/', $line);
    $scrambled = array_map('sort_str', $scrambled);
    $outputs = array_slice($scrambled, -4);
    usort($scrambled, function($a,$b) {return strlen($a) <=> strlen($b);});
    $scrambled = array_unique($scrambled);

    $i = 0;
    while (array_diff($outputs, $scan)) {
        $i++;
        foreach ($scrambled as $code) {
            decode_code($scan, $code);
        }
        $scrambled = array_diff($scrambled, $scan);
    }

    $result = 0;
    $decoded = array_flip($scan);
    foreach ($outputs as $i => $code) {
        $result += $decoded[$code] * pow(10, 3-$i);
    }
    return $result;
}

$sum = 0;
foreach ($entries as $n => $line) {
    $sum += decode_line($line);
}

echo $sum;


