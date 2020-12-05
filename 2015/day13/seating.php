<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

$weights = [];

foreach ($inputs as $line) {
    preg_match('/(?P<person>\w+) would (?P<type>gain|lose) (?P<points>\d+) happiness units? by sitting next to (?P<neighbor>\w+)/', $line, $m);
    $weight = intval($m['points']);
    if ($m['type'] === 'lose') {
        $weight *= -1;
    }
    $weights = array_merge_recursive(
        $weights,
        [
            $m['person'] => [
                $m['neighbor'] => $weight,
            ],
        ]
    );
}

function calculate_happiness($people, $weights) {
    $h = 0;
    $size = count($people);
    foreach ($people as $i => $name) {
        $left = $people[ ($i - 1 + $size)%$size ];
        $right = $people[ ($i + 1)%$size ];
        $h += $weights[$name][$left] + $weights[$name][$right];
    }
    return $h;
}

$arrangements = [];
$people = array_keys($weights);
$max = gmp_fact(count($people));

while (count($arrangements) < $max) {
    shuffle($people);
    $key = implode(',', $people);
    if (!isset($arrangements[$key])) {
        $arrangements[$key] = calculate_happiness($people, $weights);
    }
}

asort($arrangements);
echo json_encode($arrangements, JSON_PRETTY_PRINT);

$weights['me'] = [];
foreach ($people as $name) {
    $weights[$name]['me'] = $weights['me'][$name] = 0;
}
$people[] = 'me';
$arrangements = [];
$people = array_keys($weights);
$max = gmp_fact(count($people));

while (count($arrangements) < $max) {
    shuffle($people);
    $key = implode(',', $people);
    if (!isset($arrangements[$key])) {
        $arrangements[$key] = calculate_happiness($people, $weights);
    }
}

asort($arrangements);
echo json_encode($arrangements, JSON_PRETTY_PRINT);