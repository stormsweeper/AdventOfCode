<?php

$input = fopen($argv[1], 'r');

$dial = 50;
$last_pos = $dial;
$num_nums = 100;

$ends_at_zero = 0;
$ever_at_zero = 0;

while (($line = fgets($input)) !== false) {
    $last_pos = $dial;
    $total_dist = intval(substr($line, 1));
    $net_dist = $total_dist % $num_nums;

    $ever_at_zero += floor($total_dist / $num_nums);

    if ($line[0] === 'L') {
        $dial -= $net_dist;
        if ($dial < 0) {
            $dial += $num_nums;
            if ($last_pos !== 0) $ever_at_zero++;
        } elseif ($dial === 0) {
            $ever_at_zero++;
        }
    } else {
        $dial += $net_dist;
        if ($dial >= $num_nums) {
            $dial -= $num_nums;
            if ($last_pos !== 0) $ever_at_zero++;
        } elseif ($dial === 0) {
            $ever_at_zero++;
        }
    }

    if ($dial === 0) $ends_at_zero++;

    // echo "{$line} {$last_pos} {$dial} {$ends_at_zero} {$ever_at_zero}\n";
}

echo "ends at zero: {$ends_at_zero}\n";
echo "ever at zero: {$ever_at_zero}\n";
