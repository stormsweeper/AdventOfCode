<?php


$input = trim(file_get_contents($argv[1]));

function sum_numbers(string $json): int {
    preg_match_all('/-?\d+/', $json, $m);
    return array_sum($m[0] ?? []);
}
$sum_numbers_tests = [
    '[1,2,3]' => 6,
    '{"a":2,"b":4}' => 6,
    '[[[3]]]' => 3,
    '{"a":{"b":4},"c":-1}' => 3,
    '{"a":[-1,1]}' => 0,
    '[-1,{"a":1}]' => 0,
    '[]' => 0,
    '{}' => 0,
];

foreach ($sum_numbers_tests as $json => $expected) {
    assert($expected === sum_numbers($json));
}


function sum_item($item): int {
    if (is_string($item)) {
        return 0;
    }
    if (is_int($item)) {
        return $item;
    }
    if (isset($item['red'])) {
        return 0;
    }
    if (
        array_keys($item) !== array_keys(array_values($item))
        && in_array('red', $item, true)
    ) {
        return 0;
    }
    return array_sum(array_map('sum_item', $item));
}

assert(sum_item(json_decode('[1,2,3]', true)) === 6);
assert(sum_item(json_decode('[1,{"c":"red","b":2},3]', true)) === 4);
assert(sum_item(json_decode('{"d":"red","e":[1,2,3,4],"f":5}', true)) === 0);
assert(sum_item(json_decode('[1,"red",5]', true)) === 6);

$part1 = sum_numbers($input);
$part2 = sum_item(json_decode($input, true));

echo "Part 1: {$part1}\nPart 2: {$part2}\n";