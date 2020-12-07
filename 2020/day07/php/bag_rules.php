<?php

$inputs = trim(file_get_contents($argv[1]));

function parse_rules(string $rules): array {
    preg_match_all('#([\w ]+?) bags contain ([\w ,]+? bags?)\.#', $rules, $mo);
    $outer = $mo[1];
    $inner = array_map(
        function($bags) {
            if (preg_match_all('#(\d+) ([\w ]+?) bags?#', $bags, $mi)) {
                return array_combine($mi[2], $mi[1]);
            }
            return [];
        },
        $mo[2]
    );
    return array_combine($outer, $inner);
}

$rules = parse_rules($inputs);

# Part 1
$allowed_outer_bags = [];

function allowed_outers(array $inners, array $rules): array {
    $outers = [];
    foreach ($rules as $outer => $allowed_inners) {
        if (array_intersect(array_keys($allowed_inners), $inners)) {
            $outers[] = $outer;
        }
    }
    return $outers;
}

$inners = ['shiny gold'];

while ($inners) {
    $outers = allowed_outers($inners, $rules);
    $allowed_outer_bags = array_merge($allowed_outer_bags, $outers);
    $inners = $outers;
}

$allowed_outer_bags = array_unique($allowed_outer_bags);

echo "Part 1: " . count($allowed_outer_bags) . "\n";

# Part 2
$outers = [
    'shiny gold' => 1
];

$required_inner = 0;

function required_inners(array $outers, array $rules): array {
    $req = [];
    foreach ($outers as $outer => $qtyo) {
        foreach (($rules[$outer] ?? []) as $inner => $qtyi) {
            $req[$inner] = ($req[$inner] ?? 0) + $qtyo * $qtyi;
        }
    }
    return $req;
}

while ($outers) {
    $inners = required_inners($outers, $rules);
    $required_inner += array_sum($inners);
    $outers = $inners;
}

echo "Part 2: {$required_inner}\n";