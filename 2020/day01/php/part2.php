<?php

$target = 2020;
$inputs = trim(file_get_contents($argv[1]));
$inputs = array_map('intval', explode("\n", $inputs));
$inputs = array_filter($inputs, function($i) use ($target) {return $i <= $target;});
rsort($inputs);
$len = count($inputs);

for ($a = 0; $a < $len - 2; $a++) {
    for ($b = $a + 1; $b < $len - 1; $b++) {
        for ($c = $b + 1; $c < $len; $c++) {
            if (($inputs[$a] + $inputs[$b] + $inputs[$c]) === $target) {
                break 3;
            }
        }
    }
}
print_r([$inputs[$a], $inputs[$b], $inputs[$c], $inputs[$a] * $inputs[$b] * $inputs[$c]]);