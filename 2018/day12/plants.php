<?php

$input = array_filter(array_map('trim', file($argv[1])));

function toArray($string, $compact = true) {
    $arr = array_map(
        function($c) {
            return $c === '#' ? 1 : 0;
        },
        str_split($string)
    );
    if ($compact) {
        $arr = array_filter($arr);
    }
    return $arr;
}


[,, $initial] = explode(' ', array_shift($input));

$transforms = array_map(
    function($rule) {
        $t = [];
        $t['match'] = toArray(substr($rule, 0, 5), false);
        $t['replace'] = toArray(substr($rule, 9, 1), false)[0];
        return $t;
    },
    $input
);

function plantAt($plants, $pos) {
    return $plants[$pos] ?? 0;
}
function getNext($plants, $rule, $pos) {
    $current = [
        plantAt($plants, $pos - 2),
        plantAt($plants, $pos - 1),
        plantAt($plants, $pos - 0),
        plantAt($plants, $pos + 1),
        plantAt($plants, $pos + 2),
    ];
    if ($current === $rule['match']) {
        return $rule['replace'];
    }
    return false;
}


$plants = toArray($initial);

$gens = intval($argv[2] ?? 20);
$mod = pow(10, floor(log10($gens))) / 5;

for ($g = 1; $g <= $gens; $g++) {
    if ($g % $mod === 0) {
        echo "Performing gen {$g}\n";
    }
    $next = [];
    $alive = array_keys($plants);
    $lpos = min($alive) - 2;
    $rpos = max($alive) + 2;
    foreach (range($lpos, $rpos) as $pos) {
        foreach ($transforms as $rule) {
            $m = getNext($plants, $rule, $pos);
            if ($m) {
                $next[$pos] = 1;
            }
        }
    }
    $plants = array_filter($next);
}

echo array_sum(array_keys($plants));