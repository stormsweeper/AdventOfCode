<?php

$input = trim(file_get_contents($argv[1]));
$input = explode("\n", $input);

class Point {
    public $x, $y, $color, $length;
    public $top, $bottom, $left, $right, $is_end = false;

    function __construct(int $x, int $y, int $color, int $length = 0) {
        $this->x = $x;
        $this->y = $y;
        $this->color = $color;
        $this->length = $length;
    }

    static function fromPoint(Point $other): Point {
        return new Point($other->x, $other->y, $other->color, $other->length);
    }

    function dist(Point $other): int {
        return abs($this->x - $other->x) + abs($this->y - $other->y);
    }

    function originDist(): int {
        return $this->dist(new Point(0, 0, -1));
    }

    static function compare(Point $a, Point $b): int {
        $xdiff = $a->x <=> $b->x;
        if ($xdiff === 0) {
            return $a->y <=> $b->y;
        }
        return $xdiff;
    }

    function sameLoc(Point $other): bool {
        return Point::compare($this, $other) === 0;
    }

    function key(): string {
        return print_r($this, true);
    }
}

function nextSegment(Point $prev, string $next): array {
    $dir = $next[0];
    $dist = intval(substr($next, 1));
    if ($dir === 'U') {
        $top = Point::fromPoint($prev);
        $bottom = Point::fromPoint($prev);
        $top->bottom = $bottom;
        $bottom->top = $top;
        $top->y += $dist;
        $top->length += $dist;
        $top->is_end = true;
        return [$bottom, $top];
    } elseif ($dir === 'D') {
        $top = Point::fromPoint($prev);
        $bottom = Point::fromPoint($prev);
        $top->bottom = $bottom;
        $bottom->top = $top;
        $bottom->y -= $dist;
        $bottom->length += $dist;
        $bottom->is_end = true;
        return [$top, $bottom];
    } elseif ($dir === 'L') {
        $left = Point::fromPoint($prev);
        $right = Point::fromPoint($prev);
        $left->right = $right;
        $right->left = $left;
        $left->x -= $dist;
        $left->length += $dist;
        $left->is_end = true;
        return [$right, $left];
    } elseif ($dir === 'R') {
        $left = Point::fromPoint($prev);
        $right = Point::fromPoint($prev);
        $left->right = $right;
        $right->left = $left;
        $right->x += $dist;
        $right->length += $dist;
        $right->is_end = true;
        return [$left, $right];
    }
    throw new RuntimeException('Could not parse next: ' . $next);
}

$points = [];
foreach ($input as $color => $wire) {
    $prev = new Point(0, 0, $color);
    foreach (explode(',', $wire) as $step) {
        $points = array_merge($points, nextSegment($prev, $step));
        $prev = $points[count($points) - 1];
    }
}

usort($points, 'Point::compare');

$horizontals = [];
$intersections = [];
foreach ($points as $point) {
    // left point, add segment to the consideration list
    if (isset($point->right)) {
        $horizontals[$point->key()] = $point;
        continue;
    }
    // right point, remove segment from the consideration list
    if (isset($point->left)) {
        unset($horizontals[$point->left->key()]);
        continue;
    }
    // top point, check consideration list for lines that intersect
    if (isset($point->bottom)) {
        $crossing = array_filter(
            $horizontals,
            function(Point $l) use ($point) {
                return $l->color !== $point->color && ($l->y < $point->y) && ($l->y > $point->bottom->y);
            }
        );
        foreach ($crossing as $c) {
            $int = Point::fromPoint($point);
            $int->y = $c->y;
            $horz_end = $c->is_end ? $c : $c->right;
            $horz_length = $horz_end->length - $horz_end->dist($int);
            $vert_end = $point->is_end ? $point : $point->bottom;
            $vert_length = $vert_end->length - $vert_end->dist($int);
            $int->length = $horz_length + $vert_length;
            $intersections[] = $int;
        }
    }
}

// get absolute closest
usort(
    $intersections,
    function (Point $a, Point $b) {
        return $a->originDist() <=> $b->originDist();
    }
);
$closest = ($intersections[0])->originDist();

// get closest by wire length
usort(
    $intersections,
    function (Point $a, Point $b) {
        return $a->length <=> $b->length;
    }
);
$shortest = ($intersections[0])->length;

echo
"closest: {$closest}
shortest: {$shortest}
";
