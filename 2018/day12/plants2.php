<?php

ini_set('memory_limit', '1G');


$input = array_filter(array_map('trim', file($argv[1])));

[,, $initial] = explode(' ', $input[0]);

function sumPots($pots, $offset) {
    $m = strlen($pots);
    $sum = 0;
    for ($i = 0; $i < $m; $i++) {
        if ($pots[ $i ] === '#') {
            $sum += $i - $offset;
        }
    }
    return $sum;
}

$transforms = [];
foreach (array_slice($input, 1) as $rule) {
    [$search, $change] = explode(' => ', $rule);
    $transforms[$search] = $change;
}


$offset = 0;
$current = $initial;


$previous = null;
$previous_sum = null;

    //echo "$current\n";
$gens = intval($argv[2] ?? 20);
$mod = pow(10, floor(log10($gens)) - 2);
$mod = max(10, $mod);
$mod = 1000;
$last = null;
$last_offset = null;

for ($g = 1; $g <= $gens; $g++) {
    if ($g % $mod === 0) {
        echo "Performing gen {$g}\n";
    }
    $previous = $current;
    $previous_sum = sumPots($previous, $offset);
    $padded = ".....{$current}.....";
    $offset += 5;
    $m = strlen($padded);
    $next = str_repeat('.', $m);
    foreach ($transforms as $search => $change) {
        $pos = 0;
        while (($pos = strpos($padded, $search, $pos)) !== false) {
            $next[$pos + 2] = $change;
            $pos++;
        }
    }
    $next = ltrim($next, '.');
    $offset -= $last_offset = strlen($padded) - strlen($next);
    $next = rtrim($next, '.');
    $current_sum = sumPots($next, $offset);
    $sum_diff = $previous_sum - $current_sum;
    echo "gen {$g}: {$previous_sum} - {$current_sum} = {$sum_diff}\n";
    if ($current === $next) {
        echo "equals at {$g}\n";
        echo sumPots($current, $offset) - $previous_sum . "\n"; 
        break;
    }
    $current = $next;
    //echo "$current\n";
}

echo $current_sum - ($previous_sum - $current_sum) * ($gens - $g); 
