<?php

$puzzle = explode("\n", trim(file_get_contents($argv[1])));

$height = count($puzzle);
$width = strlen($puzzle[0]);

$search_term = 'xmas';

enum Worddir {
    case N;
    case NE;
    case E;
    case SE;
    case S;
    case SW;
    case W;
    case NW;

    function dx(int $step = 1): int {
        return match ($this) {
            Worddir::N, Worddir::S => 0,
            Worddir::W, Worddir::NW, Worddir::SW => 0 - $step,
            Worddir::E, Worddir::NE, Worddir::SE => $step,
        };
    }

    function dy(int $step = 1): int {
        return match ($this) {
            Worddir::E, Worddir::W => 0,
            Worddir::N, Worddir::NW, Worddir::NE => 0 - $step,
            Worddir::S, Worddir::SW, Worddir::SE => $step,
        };
    }
}

class LPos {
    function __construct(public Worddir $wdir, public int $x, public int $y) {}
    function next(int $step = 1): ?LPos {
        global $width, $height;
        $x = $this->x + $this->wdir->dx($step);
        $y = $this->y + $this->wdir->dy($step);

        if ($x < 0 || $y < 0 || $x >= $width || $y >= $height) return null;

        return new LPos($this->wdir, $x, $y);
    }
}

$words_found = 0;
$x_mas_found = 0;
for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        $words_found += find_all_terms('XMAS', $x, $y);
        if (ltr_at($x, $y) === 'A') {
            $x_found = find_x_terms('MAS', $x, $y);
            $x_mas_found += $x_found;
        }
    }
}

echo "p1: {$words_found}\n";
echo "p2: {$x_mas_found}\n";

// find x
    // scan for mas in all dirs
        // increment count
//

function find_all_terms(string $term, int $x, int $y): int {
    $found = 0;
    foreach (Worddir::cases() as $wdir) {
        $found += find_term($term, new LPos($wdir, $x, $y));
    }
    return $found;
}

function find_term(string $term, ?LPos $lpos, int $wpos = 0): int {
    if (!isset($lpos)) return 0;

    if (ltr_at($lpos->x, $lpos->y) !== $term[$wpos]) return 0;

    if ($wpos === strlen($term) - 1) return 1;

    return find_term($term, $lpos->next(), $wpos + 1);
}

function ltr_at(int $x, int $y): ?string {
    global $puzzle, $width, $height;
    if ($x < 0 || $y < 0 || $x >= $width || $y >= $height) return null;

    return $puzzle[$y][$x];
}

function find_x_terms(string $term, int $cx, int $cy): int {
    return find_diagonal_term($term, [Worddir::NW, Worddir::SE], $cx, $cy) && find_diagonal_term($term, [Worddir::NE, Worddir::SW], $cx, $cy);
}

function find_diagonal_term(string $term, array $wdirs, int $cx, int $cy): int {
    $step = 0 - floor(strlen($term) / 2);
    return find_term($term, (new LPos($wdirs[0], $cx, $cy))->next($step)) || find_term($term, (new LPos($wdirs[1], $cx, $cy))->next($step));
}

