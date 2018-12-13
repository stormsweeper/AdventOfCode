<?php

$input = array_filter(explode("\n", rtrim(file_get_contents($argv[1]))));
$max_y = count($input);
$max_x = strlen($input[0]);

$collision = null;
$ticks = 0;

define('DIR_U', 'up');
define('DIR_D', 'down');
define('DIR_L', 'left');
define('DIR_R', 'right');

// will use as a string and translate coords into indices
$tracks = implode($input);
$tracks = str_pad($tracks, $max_x * $max_y, ' ');
// constants for easier reading
define('TRK_VERT', '|');
define('TRK_HORZ', '-');
define('TRK_UPDG', '/');
define('TRK_DNDG', '\\');
define('TRK_INTR', '+');
define('TRK_NONE', ' ');

function xytoi($x, $y): int { return $GLOBALS['max_x'] * $y + $x; }
function itoxy($i): array { return [$i % $GLOBALS['max_x'], floor($i / $GLOBALS['max_x'])]; }

function trackAt($x, $y) {
    global $tracks, $max_x, $max_y;
    if ($x < 0 || $x > $max_x - 1 || $y < 0 || $y > $max_y - 1) {
        return null;
    }
    $i = xytoi($x, $y);
    return trim($tracks[$i]);
}

function printTracks($with_carts = true): string {
    global $tracks, $carts, $max_x, $collision;
    $out = $tracks;

    if ($with_carts) {
        foreach ($carts as $cart) {
            [$x, $y] = $cart['pos'];
            $i = xytoi($x, $y);
            $out[$i] = $cart['facing'];
        }
    
        if (isset($collision)) {
            $i = xytoi($collision[0], $collision[1]);
            $out[$i] = 'X';
        }
    }

    return implode("\n", str_split($out, $max_x)) . "\n";
}

// ['facing' => '', 'pos' = [x,y], 'turns' => 0]
$carts = [];
// constants for easier reading
define('FACE_U', '^');
define('FACE_D', 'v');
define('FACE_L', '<');
define('FACE_R', '>');

function sortCarts() {
    global $carts;
    uasort(
        $carts,
        function ($a, $b) {
            [$xa, $ya] = $a['pos'];
            [$xb, $yb] = $b['pos'];
            $ydiff = $ya - $yb;
            if ($ydiff === 0) {
                return $xa - $xb;
            }
            return $ydiff;
        }
    );
}

function cartAt($x, $y) {
    global $carts, $max_x, $max_y;
    if ($x < 0 || $x > $max_x - 1 || $y < 0 || $y > $max_y + 1) {
        return null;
    }
    foreach ($carts as $cart_id => $cart) {
        if ([$x, $y] === $cart['pos']) {
            return $cart_id;
        }
    }
    return null;
}

function moveAllCarts() {
    global $carts, $collision;
    foreach (array_keys($carts) as $cart_id) {
        moveCart($cart_id);
    }
    sortCarts();
}
function moveCart($cart_id) {
    global $carts;
    $cart = $carts[$cart_id] ?? null;
    if (!$cart) {
        return;
    }

    $start_pos = [$x, $y] = $cart['pos'];
    $start_track = trackAt($x, $y);
    $start_facing = $cart['facing'];

    if ($cart_id !== 'cart10') {
        echo "cart {$cart_id} started on track {$start_track} with facing {$start_facing} at {$x},{$y}\n";
    }
    // reposition this cart
    switch($cart['facing']) {
        case FACE_U: $y--; break;
        case FACE_D: $y++; break;
        case FACE_L: $x--; break;
        case FACE_R: $x++; break;
    }

    // check for collision
    $other = cartAt($x, $y);
    if (isset($other)) {
        echo "collision of carts {$cart_id} and {$other} at {$x},{$y}\n";
        unset($carts[$cart_id], $carts[$other]);
        return;
    }

    // update pos
    $cart['pos'] = [$x, $y];

    // change facing
    $turn = null;
    $track = trackAt($x, $y);
    switch ($track) {
        case TRK_INTR:
            $turn = [DIR_L, null, DIR_R][$cart['turns'] % 3];
            $cart['turns']++;
            $cart['facing'] = nextFacing($cart['facing'], $turn);
            break;

        case TRK_UPDG:
        case TRK_DNDG:
            $turn = diagTurn($cart['facing'], $track);
            $cart['facing'] = nextFacing($cart['facing'], $turn);
            break;
    }

    if ($cart_id !== 'cart10') {
        echo "cart {$cart_id} ended on track {$track} with facing {$cart['facing']} at {$x},{$y}\n";
    }

    if (!isValidFacing($track, $cart['facing'])) {
        throw new RuntimeException("WRONG FACING {$cart['facing']}/{$start_facing} for cart {$cart_id} at {$x},{$y} on track {$track} - from track {$cart['last_track']} with facing {$cart['last_facing']}");
    }

    $cart['last_facing'] = $start_facing;
    $cart['last_pos'] = $start_pos;
    $cart['last_track'] = $start_track;
    $carts[$cart_id] = $cart;
}

function isValidFacing($track, $facing) {
    if ($track === '|') {
        return $facing === FACE_U || $facing === FACE_D;
    }
    if ($track === '-') {
        return $facing === FACE_L || $facing === FACE_R;
    }
    return true;
}

function diagTurn($facing, $track) {
    if ($track === TRK_UPDG) {
        if ($facing === FACE_U || $facing === FACE_D) {
            return DIR_R;
        }
        return DIR_L;
    }
    if ($track === TRK_DNDG) {
        if ($facing === FACE_U || $facing === FACE_D) {
            return DIR_L;
        }
        return DIR_R;
    }
    return null;
}

function nextFacing($facing, $dir) {
    if ($dir !== DIR_L && $dir !== DIR_R) {
        return $facing;
    }
    switch ($facing) {
        case FACE_U: return $dir === DIR_L ? FACE_L : FACE_R;
        case FACE_D: return $dir === DIR_L ? FACE_R : FACE_L;
        case FACE_L: return $dir === DIR_L ? FACE_D : FACE_U;
        case FACE_R: return $dir === DIR_L ? FACE_U : FACE_D;
    }
}

// init carts
preg_match_all('/[v<>\^]/', $tracks, $matches, PREG_OFFSET_CAPTURE);
foreach ($matches[0] as $m) {
    $index = $m[1];
    $facing = $m[0];
    [$x, $y] = itoxy($index);
    $cart_id = 'cart' . count($carts);

    // work out what the actual track looks like
    switch ($facing) {
        case FACE_U:
        case FACE_D:
            $tracks[$index] = TRK_VERT;
            break;
        case FACE_L:
        case FACE_R:
            $tracks[$index] = TRK_HORZ;
            break;
    }

    // set up the cart
    $carts[$cart_id] = [
        'facing' => $facing,
        'pos' => [$x, $y],
        'turns' => 0,
        'last_track' => $tracks[$index],
        'last_facing' => $facing,
    ];

}
//echo printTracks(true);

while (count($carts) > 1) {
    $ticks++;
    echo "tick {$ticks}\n";
    if ($ticks % 100000 === 0) {
        $nc = count($carts);
        echo "{$nc} carts at tick {$ticks}\n";
    }
    moveAllCarts();
}

//echo printTracks(true);
//print_r($carts);
$last = array_pop($carts);
[$x, $y] = $last['pos'];
echo "last cart: {$x},{$y} at ticks: {$ticks}\n";
