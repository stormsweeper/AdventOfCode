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

// max xv is equal to max_x - it'll be in x range in 1 step
$max_xv = $xv = $max_x;

// min_vx is the smallest value where  min_x <= seqsum(xv, 0)
// rather than do the proper maths I'll just monte carlo it
// calculate the max steps for a given xv - more than this and the shot would be out of range to the right
$max_steps = [];
$t = 0;
do {
    $dist = seqsum($xv, 0);
    if (in_x_range($dist)) {
        $max_steps[$xv] = INF; // descent will always be in x range
    }
    elseif ($dist >= $min_x) {
        if ($t < $xv && seqsum($xv, $xv - $t) <= $max_x) $t++;
        $max_steps[$xv] = $t;
    }
    $min_xv = $xv;    
    $xv--;
} while ($dist >= $min_x);

// yv is trickier - 
// rather than do the proper maths I'll just monte carlo it again
$apexes = [];

// scan down, don't need to worry about arc, just cumulative distances
$yv = 0;
do {
    $found = 0;
    foreach ($max_steps as $xv => $steps) {
        $s = 1;
        do {
            $x = seqsum($xv, $xv - ($s - 1)); 
            $y = seqsum($yv, $yv - ($s - 1));
            if (in_range($x, $y)) {
                $found++;
                // non-positive apex is always 0, i.e. the launch point
                $apexes["{$xv},{$yv}"] = 0;
                break; // only need to catch the first
            }
            $s++;
        } while ($s < $steps && $y > $min_y);
    }
    // scan down
    $yv--;
} while ($found);

// scan up
$yv = 0;
do {
    $found = 0;

    // i.e. how many steps it takes to get back to y=0
    // e.g. for yv=2: y is 2, 3, 3, 2, 0
    // e.g. for yv=3: y is 3, 5, 6, 6, 5, 3, 0
    $loft_steps = 2 * $yv + 1;

    // apex for pos yv is seqsum(yv, 0), i.e. how high it gets when yv hits 0
    $apex = seqsum($yv, 0);

    // yv after loft steps is 1 less than the neg absolute of initial yv
    // e.g. for yv=2: 1, 0, -1, -2, -3
    $desc_yv = 0 - $yv - 1;

    foreach ($max_steps as $xv => $steps) {

        // if the loft is longer than the time it would be in x range, it can't hit
        if ( $loft_steps > $steps) continue;

        // xv after loft steps is just straight subtraction, but minimum 0 (i.e. we never go backwards)
        // e.g. for xv=7,yv=2: 6, 5, 4, 3, 2
        // e.g. for xv=6,yv=3: 5, 4, 3, 2, 1, 0, 0
        // e.g. for xv=6,yv=2: 5, 4, 3, 2, 1
        // e.g. for xv=6,yv=9: 5, 4, 3, 2, 1, 0, 0, 0, ... 0
        $desc_xv = max(0, $xv - $loft_steps);

        // dist of the arc when it returns to y=0
        // e.g. for xv=7,yv=2: (7,2), (13,3), (18,3), (22,2), (25,0)
        // e.g. for xv=6,yv=3: (6,3), (11,5), (15,6), (18,6), (20,5), (21,3), (21,0)
        // e.g. for xv=6,yv=2: (6,2), (11,3), (15,3), (18,2), (20,0)
        // e.g. for xv=6,yv=1: (6,1), (11,1), (15,0)
        // so sequence is a=xv, b=desc_xv + 1
        // e.g. for xv=7,yv=2: a=7, b=3 -> 25
        // e.g. for xv=6,yv=3: a=6, b=1 -> 21
        // e.g. for xv=6,yv=2: a=6, b=2 -> 20
        // e.g. for xv=6,yv=1: a=6, b=4 -> 15
        // e.g. for xv-6,yv=9: a=6, b=1 -> 21

        $loft_x = seqsum($xv, $desc_xv);

        $s = 1;
        do {
            // will be at least this far 
            $x = $loft_x;
            // if we had descent x velocity, calculate the extra per step
            if ($desc_xv) $x += seqsum($desc_xv, $desc_xv - ($s - 1));

            // calculate how far it descends by this step
            // e.g. for yv=2:  seqsum(-3, -3) -> -3, seqsum(-3, -4) -> -7
            // e.g. for yv=3:  seqsum(-4, -4) -> -4, seqsum(-4, -5) -> -9
            // e.g. for yv=9:  seqsum(-10, -10) -> -10 (just hits)
            // e.g. for yv=10: seqsum(-11, -11) -> -11 (overshoots)
            $y = seqsum($desc_yv, $desc_yv - ($s - 1));

            if (in_range($x, $y)) {
                $found++;
                // non-positive apex is always 0, i.e. the launch point
                $apexes["{$xv},{$yv}"] = $apex;
                break; // only need to catch the first
            }
            $s++;
        } while ($s < $steps && $y > $min_y);
    }
    // scan up
    $yv++;
} while ($found);

$p1 = max($apexes);
$p2 = count($apexes);

echo "p1:{$p1} p2:{$p2}\n";