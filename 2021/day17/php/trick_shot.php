<?php

// puzzle input
$target_desc = trim($argv[1]);

// example target
$target_desc = 'target area: x=20..30, y=-10..-5';


preg_match('/x=(\d+)\.\.(\d+), y=(-?\d+)\.\.(-?\d+)/', $target_desc, $m);
[, $min_x, $max_x, $min_y, $max_y] = $m;


function in_x_range(int $x): bool {
    global $min_x, $max_x;
    return $x >= $min_x && $x <= $max_x;
}

function in_y_range(int $y): bool {
    global $min_y, $max_y;
    return $y >= $min_y && $y <= $max_y;
}

function in_range(int $x, int $y): bool {
    return in_x_range($x) && in_y_range($y);
}

// seq sum of [a..b] is avg(a,b) * count(seq)
function seqsum(int $a, int $b): int {
    return ($a+$b)/2*(abs($a-$b)+1);
}

// max xv is equal to max_x - it'll be in x range for only 1 step
// min_vx is the smallest value where  min_x <= seqsum(xv, 0)
$xv = $max_x;

// we should always have 1 step min
$min_s = 1; 

// this will be a map of xv => [min steps, max steps]
$x_steps = [];
do {
    $max_dist = seqsum($xv, 0);

    // if the terminal xv is hit, and is in range the max steps can be infinite
    $max_s = INF;
    if ($xv < $min_x) {
        while (seqsum($xv, $xv - ($min_s - 1)) < $min_x) $min_s++;
    }
    if ($max_dist > $max_x) {
        $max_s = $min_s;
        while (seqsum($xv, $xv - ($max_s)) <= $max_x) $max_s++;
    }
    $x_steps[$xv] = [$min_s, $max_s];
    $xv--;
} while ($xv > 0);

// min yv is similar to max xv, if you go more negative, it will overshoot on first step
$yv = $min_y;

// we should always have 1 step min
$min_s = 1; 

// similar to above, just remember that we're in negatives so "min" and "max" are reversed in magnitude
$y_steps = [];
do {
    if ($yv > $max_y) {
        while (seqsum($yv, $yv - ($min_s - 1)) > $max_y) $min_s++;
    }
    $max_s = $min_s;
    if ($yv > $min_y) {
        while (seqsum($yv, $yv + ($max_s - 1)) > $min_y) $max_s++;
    }

    $y_steps[$yv] = [$min_s, $max_s];

    // calculate the non-negative postive yv
    // yv after the arc is less than the neg absolute of initial yv
    // e.g. for yv=0: -1
    // e.g. for yv=1: 0, -1, -2
    // e.g. for yv=2: 1, 0, -1, -2
    // e.g. for yv=3: 2, 1, 0, -1, -2, -3, -4
    // so the corresponding non-negative yv is simply abs(min_yv) - 1
    // e.g. yv=-1 corresponds to yv=0
    // e.g. yv=-2 corresponds to yv=1
    $nn_yv = abs($yv) - 1;

    // i.e. how many steps it takes to start descending y=0 if yv >= 0
    // e.g. fpr yv=0: y is 0
    // e.g. for yv=1: y is 1, 1, 0
    // e.g. for yv=2: y is 2, 3, 3, 2, 0
    // e.g. for yv=3: y is 3, 5, 6, 6, 5, 3, 0
    $loft_steps = ($nn_yv * 2) + 1;

    // 
    $y_steps[$nn_yv] = [$min_s + $loft_steps, $max_s + $loft_steps];

    $yv++;
} while ($yv < 0);

print_r($y_steps); exit;

// yv after the arc is less than the neg absolute of initial yv
// e.g. for yv=1: 0, -1, -2
// e.g. for yv=2: 1, 0, -1, -2
// e.g. for yv=3: 2, 1, 0, -1, -2, -3, -4
// we already know above the min yv that will still be in y range, the after-arc yv can only be that low
// thus max yv is simply abs(min_yv) - 1
$max_yv = abs($min_yv) - 1;

// rather than do the proper maths I'll just monte carlo it again
$apexes = [];

for ($yv = $min_yv; $yv <= $max_yv; $yv++) {

    // for yv <= 0, apex is always 0
    $apex = 0;
    // apex for yv > 0 is seqsum(yv, 0), i.e. how high it gets when yv hits 0
    if ($yv > 0) $apex = seqsum($yv, 0);

    // for yv <= 0, initial descent v is equal to yv
    $dv = $yv;
    // for yv > 0, again descent velocity is 1 less than abs neg of yv
    if ($yv > 0) $dv = 0 - $yv - 1;

    // i.e. how many steps it takes to get back to y=0 if yv > 0
    // e.g. for yv=1: y is 1, 1, 0
    // e.g. for yv=2: y is 2, 3, 3, 2, 0
    // e.g. for yv=3: y is 3, 5, 6, 6, 5, 3, 0
    $loft_steps = 0;
    if ($yv > 0) $loft_steps = 2 * $yv + 1;

    for ($xv = $min_xv; $xv <= $max_xv; $xv++) {
        $s = 1;
        do {
            $x = seqsum($xv, $xv - ($s - 1));
            if ($loft_steps && $loft_steps >= $s) {
                $y = 0;
            }
            else {
                $y = seqsum($dv, $dv - ($s - $loft_steps - 1));
            }

            if (in_range($x, $y)) {
                // non-positive apex is always 0, i.e. the launch point
                $apexes["{$xv},{$yv}"] = $apex;
                break; // only need to catch the first
            }
            $s++;
        } while ($y >= $min_y);
    }
}

$p1 = max($apexes);
$p2 = count($apexes);

echo "p1:{$p1} p2:{$p2}\n";

// used to suss out what I did wrong
$example_results = '23,-10  25,-9   27,-5   29,-6   22,-6   21,-7   9,0     27,-7   24,-5
25,-7   26,-6   25,-5   6,8     11,-2   20,-5   29,-10  6,3     28,-7
8,0     30,-6   29,-8   20,-10  6,7     6,4     6,1     14,-4   21,-6
26,-10  7,-1    7,7     8,-1    21,-9   6,2     20,-7   30,-10  14,-3
20,-8   13,-2   7,3     28,-8   29,-9   15,-3   22,-5   26,-8   25,-8
25,-6   15,-4   9,-2    15,-2   12,-2   28,-9   12,-3   24,-6   23,-7
25,-10  7,8     11,-3   26,-7   7,1     23,-9   6,0     22,-10  27,-6
8,1     22,-8   13,-4   7,6     28,-6   11,-4   12,-4   26,-9   7,4
24,-10  23,-8   30,-8   7,0     9,-1    10,-1   26,-5   22,-9   6,5
7,5     23,-6   28,-10  10,-2   11,-1   20,-9   14,-2   29,-7   13,-3
23,-5   24,-8   27,-9   30,-7   28,-5   21,-10  7,9     6,6     21,-5
27,-10  7,2     30,-9   21,-8   22,-7   24,-9   20,-6   6,9     29,-5
8,-2    27,-8   30,-5   24,-7';

$example_results = preg_split('/\s+/s', $example_results);

$missing_results = array_diff($example_results, array_keys($apexes));
$extra_results   = array_diff(array_keys($apexes), $example_results); 
function sort_vels($a, $b) {
    [$ax, $ay] = explode(',', $a);
    [$bx, $by] = explode(',', $b);
    $cmp = $ax <=> $bx;
    if ($cmp === 0) return $ay <=> $by;
    return $cmp;
}
usort($missing_results, 'sort_vels');
usort($extra_results, 'sort_vels');

echo 'missing results:' . json_encode($missing_results) . "\n";
echo 'extra results:  ' . json_encode($extra_results) . "\n";
