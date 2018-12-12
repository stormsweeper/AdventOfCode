<?php

$input = array_filter(array_map('trim', file($argv[1])));

$literals = array_map('strlen', $input);
$values = array_map(
    function($literal) {
        return eval("return strlen({$literal});");
    },
    $input
);
$doubled = array_map(
    function($literal) {
        return strlen(strtr($literal, ['\\' => '\\\\', '"' => '\\"'])) + 2;
    },
    $input
);

echo "part 1: " . (array_sum($literals) - array_sum($values)) . "\n";
echo "part 2: " . (array_sum($doubled) - array_sum($literals)) . "\n";



