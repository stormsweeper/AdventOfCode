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

function prior_steps(string $start, array $replacements): array {
    $prior = [];
    foreach ($replacements as $from => $tos) {
        foreach ($tos as $to) {
            $tlen = strlen($to);
            foreach (strposes($start, $to) as $p) {
                $r = substr_replace($start, $from, $p, $tlen);
                if (strpos($r, 'e') !== false && $r !== 'e') continue;
                $prior[$r] = 1;
            }
        }
    }
    return $prior;
}

$visited = $consider = [$target => $target];
$its = 0;
while (!isset($consider['e'])) {
    $its++;
    $next = [];
    foreach ($consider as $mol => $_) {
        foreach (prior_steps($mol, $replacements) as $prior => $_) {
            if (isset($visited[$prior])) continue;
            $next[$prior] = $visited[$prior] = 1;
        }
    }
    $consider = $next;
}

echo "Part 2: {$its}\n";