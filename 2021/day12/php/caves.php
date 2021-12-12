<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

function is_little(string $cave): bool {
    return strtolower($cave) === $cave;
}

function is_loopback(array $path): bool {
    return $path[0] !== 'start';
}

function is_valid_exit(string $cave, array $path) {
    if ($cave === 'start') return false;
    if ($cave === 'end' || !is_little($cave)) return true;
    if ($cave !== $path[0]) return !in_array($cave, $path);
    // complicated version
    $visits = 0;
    foreach ($path as $c) {
        if ($c === $cave) $visits++;
    }
    return $visits === 2; // i.e. the marker and the first visit
}

$cave_passages = [];
foreach ($inputs as $line) {
    list($a, $b) = explode('-', $line);
    if (!isset($cave_passages[$a])) $cave_passages[$a] = [];
    if (!isset($cave_passages[$b])) $cave_passages[$b] = [];
    $cave_passages[$a][$b] = $b;
    $cave_passages[$b][$a] = $a;
}

$consider = [
    ['start'],
];
$rejected = $valid = $loopbacks = 0;

while ($consider) {
    $next = [];
    foreach ($consider as $wip_path) {
        $last = $wip_path[array_key_last($wip_path)];
        $passages = $cave_passages[$last];
        if (!$passages) {
            $rejected++;
            continue;
        }
        foreach ($passages as $exit) {
            if (!is_valid_exit($exit, $wip_path)) continue;
            if ($exit === 'end') {
                $npath = array_merge($wip_path, [$exit]);
                if (is_loopback($npath)) {
                    $loopbacks++;
                }
                else {
                    $valid++;
                }
            }
            else {
                $npath = array_merge($wip_path, [$exit]);
                $next[] = $npath;
                if (!is_loopback($npath)) {
                    array_unshift($npath, $exit);
                    $next[] = $npath;
                }
            }
        }
    }
    $consider = $next;
}

$p2 = $loopbacks + $p1;

echo "p1:{$valid} p2:{$p2}\n";