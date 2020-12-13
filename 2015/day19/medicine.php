<?php

ini_set('memory_limit', '1G');

$inputs = trim(file_get_contents($argv[1]));

list($replacements_desc, $target) = explode("\n\n", $inputs);

$replacements = [];
$backwards = [];

foreach(explode("\n", $replacements_desc) as $line) {
    list($from, $to) = explode(' => ', $line);
    if (!isset($replacements[$from])) $replacements[$from] = [];
    $replacements[$from][] = $to;
}


function strposes(string $haystack, string $needle): array {
    $offsets = []; $o = 0; $hlen = strlen($haystack);
    while ($o < $hlen && ($p = strpos($haystack, $needle, $o)) !== false) {
        $offsets[] = $p;
        $o = $p + 1;
    }
    return $offsets;
}

function next_steps(string $start, array $replacements): array {
    $next = [];
    foreach ($replacements as $from => $tos) {
        $rlen = strlen($from);
        foreach (strposes($start, $from) as $p) {
            foreach ($tos as $to) {
                $r = substr_replace($start, $to, $p, $rlen);
                $next[$r] = $r;
            }
        }
    }
    return $next;
}

$p1 = count(next_steps($target, $replacements));
echo "Part 1: {$p1}\n";
