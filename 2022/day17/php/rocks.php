<?php

abstract class Rock {
    protected array $blocks;
    abstract function __construct(int $base = 0);

    function blocks(): array { return $this->blocks; }
    function height(): int {
        return $this->top_y() - $this->bottom_y() + 1;
    }

    function left_x(): int {
        $left = PHP_INT_MAX;
        foreach ($this->blocks as [$x, $y]) {
            $left = min($x, $left);
        }
        return $left;
    }

    function right_x(): int {
        $right = -1;
        foreach ($this->blocks as [$x, $y]) {
            $right = max($x, $right);
        }
        return $right;
    }

    function bottom_y(): int {
        $bottom = PHP_INT_MAX;
        foreach ($this->blocks as [$x, $y]) {
            $bottom = min($y, $bottom);
        }
        return $bottom;
    }

    function top_y(): int {
        $top = -1;
        foreach ($this->blocks as [$x, $y]) {
            $top = max($y, $top);
        }
        return $top;
    }

    function shift(int $dx): bool {
        return $this->move($dx, 0);
    }

    function drop(): bool {
        return $this->move(0, -1);
    }

    protected function move(int $dx, int $dy): bool {
        return false;
    }
}

class HorzLine extends Rock {
    function __construct(int $base = 0) {
        $this->blocks = [
            [2, $base + 3], [3, $base + 3], [4, $base + 3], [5, $base + 3],
        ];
    }
}

(function(){
    $rock = new HorzLine();
    assert($rock->height() === 1, 'HorzLine should be 1 row tall');
    assert($rock->top_y() === 3, 'HorzLine top should be at 3 blocks over base');
    assert($rock->bottom_y() === 3, 'HorzLine should appear at 3 blocks over base');
    assert($rock->left_x() === 2, 'HorzLine should appear at 2 blocks from left');
    assert($rock->right_x() === 5, 'HorzLine should appear at 2 blocks from right');
})();

class Cross extends Rock {
    function __construct(int $base = 0) {
        $this->blocks = [
                            [3, $base + 5],
            [2, $base + 4], [3, $base + 4], [4, $base + 4],
                            [3, $base + 3],
        ];
    }
}

(function(){
    $rock = new Cross();
    assert($rock->height() === 3, 'Cross should be 3 rows tall');
    assert($rock->top_y() === 5, 'Cross top should be at 5 blocks over base');
    assert($rock->bottom_y() === 3, 'Cross should appear at 3 blocks over base');
    assert($rock->left_x() === 2, 'Cross should appear at 2 blocks from left');
    assert($rock->right_x() === 4, 'Cross should appear at 3 blocks from right');
})();

class Ell extends Rock {
    function __construct(int $base = 0) {
        $this->blocks = [
                                            [4, $base + 5],
                                            [4, $base + 4],
            [2, $base + 3], [3, $base + 3], [4, $base + 3],
        ];
    }
}

(function(){
    $rock = new Ell();
    assert($rock->height() === 3, 'Ell should be 3 rows tall');
    assert($rock->top_y() === 5, 'Ell top should be at 5 blocks over base');
    assert($rock->bottom_y() === 3, 'Ell should appear at 3 blocks over base');
    assert($rock->left_x() === 2, 'Ell should appear at 2 blocks from left');
    assert($rock->right_x() === 4, 'Ell should appear at 3 blocks from right');
})();

class VertLine extends Rock {
    function __construct(int $base = 0) {
        $this->blocks = [
            [2, $base + 6],
            [2, $base + 5],
            [2, $base + 4],
            [2, $base + 3],
        ];
    }
}

(function(){
    $rock = new VertLine();
    assert($rock->height() === 4, 'VertLine should be 4 rows tall');
    assert($rock->top_y() === 6, 'VertLine top should be at 6 blocks over base');
    assert($rock->bottom_y() === 3, 'VertLine should appear at 3 blocks over base');
    assert($rock->left_x() === 2, 'VertLine should appear at 2 blocks from left');
    assert($rock->right_x() === 2, 'VertLine should appear at 5 blocks from right');
})();

class Square extends Rock {
    function __construct(int $base = 0) {
        $this->blocks = [
            [2, $base + 4], [2, $base + 4],
            [2, $base + 3], [3, $base + 3],
        ];
    }
}

(function(){
    $rock = new Square();
    assert($rock->height() === 2, 'Square should be 4 rows tall');
    assert($rock->top_y() === 4, 'Square top should be at 4 blocks over base');
    assert($rock->bottom_y() === 3, 'Square should appear at 3 blocks over base');
    assert($rock->left_x() === 2, 'Square should appear at 2 blocks from left');
    assert($rock->right_x() === 3, 'Square should appear at 4 blocks from right');
})();

