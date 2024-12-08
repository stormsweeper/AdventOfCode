<?php

[$rules_input, $updates] = explode("\n\n", trim(file_get_contents($argv[1])));

$rules = [];
foreach (explode("\n", $rules_input) as $rule) {
    [$before, $after] = explode('|', $rule);
    if (!isset($rules[$before])) $rules[$before] = [];
    $rules[$before][$after] = true;
}

$p1 = 0;
foreach (explode("\n", $updates) as $update) {
    $update = array_map('intval', explode(',', $update));
    if (is_correct($update, $rules)) $p1 += middle_val($update);
}

echo "p1: {$p1}\n";

function is_correct(array $update, array $rules): bool {
    $len = count($update);
    for ($i = 1; $i < $len; $i++) {
        if (!isset($rules[$update[$i]])) continue;
        for ($j = 0; $j < $i; $j++) {
            if (isset($rules[$update[$i]][$update[$j]])) return false;
        }
    }
    return true;
}

function middle_val(array $vals): int {
    $i = floor(count($vals) / 2);
    return $vals[$i];
}