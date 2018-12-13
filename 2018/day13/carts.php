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
// constants for easier reading
define('TRK_VER', '|');
define('TRK_HOR', '-');
define('TRK_UPD', '/');
define('TRK_DND', '\\');
define('TRK_INT', '+');

function xytoi($x, $y): int { return $GLOBALS['max_x'] * $y + $x; }
function itoxy($i): array { return [$i % $GLOBALS['max_x'], floor($i / $GLOBALS['max_x'])]; }

function trackAt($x, $y) {
    global $tracks, $max_x, $max_y;
    if ($x < 0 || $x > $max_x - 1 || $y < 0 || $y > $max_y + 1) {
        return null;
    }
    return trim($tracks[xytoi($x, $y)]);
}

function neigboringTracks($x, $y): array {
    global $tracks, $max_x, $max_y;
    $n = [
        DIR_U   => trackAt($x, $y - 1),
        DIR_D   => trackAt($x, $y + 1),
        DIR_L   => trackAt($x - 1, $y),
        DIR_R   => trackAt($x + 1, $y),
    ];
    return array_filter($n);
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
    usort(
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
    foreach ($carts as $cart) {
        if ([$x, $y] === $cart['pos']) {
            return $cart;
        }
    }
    return null;
}

function moveAllCarts() {
    global $carts, $collision;
    foreach ($carts as &$cart) {
        if (isset($collision)) {
            break;
        }
        moveCart($cart);
    }
}
function moveCart(&$cart) {
    [$x, $y] = $cart['pos'];

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
        $GLOBALS['collision'] = [$x, $y];
        return;
    }

    // update pos
    $cart['pos'] = [$x, $y];

    // change facing
    $turn = null;
    $track = trackAt($x, $y); 
    switch ($track) {
        case TRK_INT:
            $turn = [DIR_L, null, DIR_R][$cart['turns'] % 3];
            $cart['turns']++;
            break;

        case TRK_UPD:
        case TRK_DND:
            $turn = diagTurn($cart['facing'], $track);
            break;
    }
    $cart['facing'] = nextFacing($cart['facing'], $turn);
}

function diagTurn($facing, $track) {
    if ($track === TRK_UPD) {
        if ($facing === FACE_U || $facing === FACE_D) {
            return DIR_R;
        }
        return DIR_L;
    }
    if ($track === TRK_DND) {
        if ($facing === FACE_U || $facing === FACE_D) {
            return DIR_L;
        }
        return DIR_R;
    }
    return null;
}

function nextFacing($facing, $dir) {
    if (!$dir) {
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

    // set up the cart
    $carts[] = [
        'facing' => $facing,
        'pos' => [$x, $y],
        'turns' => 0,
    ];

    // work out what the actual track looks like
    $neighbors = neigboringTracks($x, $y);
    if (count($neighbors) === 4) {
        $tracks[$index] = TRK_INT;
    }
    elseif(isset($neighbors['up']) && isset($neighbors['down'])) {
        $tracks[$index] = TRK_VER;
    }
    elseif(isset($neighbors['left']) && isset($neighbors['right'])) {
        $tracks[$index] = TRK_HOR;
    }
    elseif(isset($neighbors['left']) && isset($neighbors['up'])) {
        $tracks[$index] = TRK_UPD;
    }
    elseif(isset($neighbors['right']) && isset($neighbors['down'])) {
        $tracks[$index] = TRK_UPD;
    }
    elseif(isset($neighbors['left']) && isset($neighbors['down'])) {
        $tracks[$index] = TRK_DND;
    }
    elseif(isset($neighbors['right']) && isset($neighbors['up'])) {
        $tracks[$index] = TRK_DND;
    }
}

while (!isset($collision)) {
    $ticks++;
    moveAllCarts();
    sortCarts();
}

//echo printTracks();

echo "collision: {$collision[0]},{$collision[1]} at ticks: {$ticks}\n";
