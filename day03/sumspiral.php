<?php

// $spiral[x][y];
$spiral = [0 => [0 => 1]];

// calculates the sum based on adjacent squares
function squaresum($coords) {
    global $spiral;
    list ($x, $y) = $coords;

    if (isset($spiral[$x][$y])) {
        return $spiral[$x][$y];
    }

    $sums = array_map(
        function($square) {
            return squaresum($square);
        },
        adjacent($coords)
    );
    return $spiral[$x][$y] = array_sum($sums);
}

function pos_ring($pos) {
    return ((ceil(sqrt($pos)) | 1) - 1) / 2;
}

function coords_ring($coords) {
    return max(abs($coords[0]), abs($coords[1]));
}

function ring_width($ring) {
    return ($ring * 2) + 1;
}

function corner_pos($ring) {
    return pow(ring_width($ring), 2);
}

// x,y
function coords($pos) {
    if ($pos === 1) {
        return [0,0];
    }

    $ring = pos_ring($pos);
    $outer_corner = corner_pos($ring);

    // corners are easy
    if ($pos === $outer_corner) {
        return [$ring, - $ring];
    }

    $diff = $outer_corner - $pos;
    $ring_leg = ring_width($ring) - 1;

    // step backwards around the ring
    if ($diff <= $ring_leg) {
        $x = $ring - $diff;
        $y = - $ring;
    } elseif ($diff <= ($ring_leg * 2)) {
        $x = - $ring;
        $y = - $ring + ($diff - $ring_leg);
    } elseif ($diff <= ($ring_leg * 3)) {
        $x = - $ring + ($diff - $ring_leg * 2);
        $y = $ring;
    } elseif ($diff <= ($ring_leg * 4)) {
        $x = $ring;
        $y = $ring - ($diff - $ring_leg * 3);
    }

    return [$x,$y];
}

function coords_pos($coords) {
    if ($coords === [0,0]) {
        return 1;
    }

    $ring = coords_ring($coords);
    $corner = corner_pos($ring);
    //$leg = 
    if ($coords === [$ring, - $ring]) {
        return $corner;
    }

    list ($px, $py) = $coords;
    $ring_leg = ring_width($ring) - 1;

    // bottom leg
    if ($py === - $ring) {
        return $corner - abs($ring - $px);
    }

    // left leg
    if ($px === - $ring) {
        return $corner - ($ring_leg) - abs(- $ring - $py);
    }

    // top leg
    if ($py === $ring) {
        return $corner - ($ring_leg * 2) - abs(- $ring - $px);
    }

    // right leg
    if ($px === $ring) {
        return $corner - ($ring_leg * 3) - abs($ring - $py);
    }

    return -1;
}

function adjacent($coords) {
    list ($x, $y) = $coords;
    if ($x === 0 && $y === 0) {
        return [];
    }

    $adj = [
        [$x + 1, $y],
        [$x - 1, $y],
        [$x + 1, $y + 1],
        [$x + 1, $y - 1],
        [$x - 1, $y + 1],
        [$x - 1, $y - 1],
        [$x, $y + 1],
        [$x, $y - 1],
    ];

    $pos = coords_pos($coords);

    $adj = array_filter(
        $adj,
        function($square) use ($pos) {
            return coords_pos($square) < $pos;
        }
    );
    return $adj;
}

$check = intval($argv[1]);
// to avoid generating the world for larger values
$pos = min(2, floor($check ** 1/3));
$last = 0;

while ($last <= $check) {
    $last = squaresum(coords($pos));
    $pos++;
}

echo $last;
