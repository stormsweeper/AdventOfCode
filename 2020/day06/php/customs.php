<?php
$forms = trim(file_get_contents($argv[1]));
$groups = explode("\n\n", $forms);

# Part 1
$yeses = 0;
$unanimous = 0;
foreach ($groups as $g) {
    // p1
    $chars = count_chars($g, 1);
    unset($chars[10]);
    $yeses += count($chars);
    // p2
    $people = array_map(
        function($p) {
            return str_split($p);
        },
        explode("\n", $g)
    );
    $match = array_intersect(
        range('a', 'z'),
        ...$people
    );
    $unanimous += count($match);
}
echo "Part 1: {$yeses}\nPart 2: {$unanimous}\n";