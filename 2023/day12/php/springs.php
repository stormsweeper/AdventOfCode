<?php

$records = trim(file_get_contents($argv[1]));
$records = explode("\n", $records);

$unfold = boolval($argv[2] ?? 0);

function check_scan(string $scan, string $pattern): int {
    $first_unknown = strpos($scan, '?');
    // no unknowns
    if ($first_unknown === false) return (int)preg_match($pattern, $scan);

    // map the first ?
    $a = $b = $scan;
    $a[$first_unknown] = '#';
    $b[$first_unknown] = '.';
    return check_scan($a, $pattern) + check_scan($b, $pattern);
}

function possible(string $record, bool $unfold): int {
    [$scan, $counts] = explode(' ', $record);
    if ($unfold) {
        $scan = implode('?', array_fill(0, 5, $scan));
        $counts = implode(',', array_fill(0, 5, $counts));
    }

    // yes, I'm dynamically making a regex pattern
    $counts_regex = '/^[.]*#{' . str_replace(',', '}[.]+#{', $counts) . '}[.]*$/';

    return check_scan($scan, $counts_regex);
}

$valid = 0;

foreach ($records as $r) {
    $valid += possible($r, $unfold);
}

echo $valid;