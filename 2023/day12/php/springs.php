<?php

$records = trim(file_get_contents($argv[1]));
$records = explode("\n", $records);

$unfold = boolval($argv[2] ?? 0);

function check_scan(string $scan, array $pattern): int {
    $first_unknown = strpos($scan, '?');
    // no unknowns
    if ($first_unknown === false) {
        $scan = preg_split('/\.+/', $scan, -1, PREG_SPLIT_NO_EMPTY);
        return $scan === $pattern ? 1 : 0;
    }

    // check if if works so far
    $left_scan = substr($scan, 0, 0-$first_unknown);
    if (strlen($left_scan) >= strlen($pattern[0])) {
        preg_match_all('/(?<=\.)#+(?=\.)/', $left_scan, $matches);
        // echo "{$left_scan}\t";
        // echo json_encode($matches[0]) . "\t";
        // echo json_encode($pattern) . "\n";
        if (array_diff($matches[0], $pattern)) {
            // echo "should skip...\n";
            return 0;
        }
    }

    // echo "continuining...\n";
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

    $counts = array_map(
        function($c) { return str_repeat('#', $c); },
        json_decode("[{$counts}]")
    );

    return check_scan($scan, $counts);
}

$valid = 0;

foreach ($records as $r) {
    $valid += possible($r, $unfold);
}

echo $valid;