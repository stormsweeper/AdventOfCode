<?php

$target = 2020;
$inputs = trim(file_get_contents($argv[1]));
$inputs = array_map('intval', explode("\n", $inputs));
$inputs = array_filter($inputs, function($i) use ($target) {return $i <= $target;});
rsort($inputs);
$len = count($inputs);

for ($i = 0; $i < $len - 1; $i++) {
    for ($j = $i + 1; $j < $len; $j++) {
        if (($inputs[$i] + $inputs[$j]) === $target) {
            break 2;
        }
    }
}
print_r([$inputs[$i], $inputs[$j], $inputs[$i] * $inputs[$j]]);