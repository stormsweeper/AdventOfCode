<?php

require_once 'rocks.php';

class Chamber {
    protected int $width = 7;
    protected int $max_height = 0;
    protected Rock $current_rock;
    protected array $chamber = [];
    protected int $vents = 0;

    function __construct(protected string $vent_pattern, protected int $num_rocks = 0) {}

    function next_vent(): int {
        $i = $this->vents++ % strlen($this->vent_pattern);
        if ($this->vent_pattern[$i] === '<') return -1;
        if ($this->vent_pattern[$i] === '>') return 1;
        throw RuntimeException('Unknown vent direction: ' . $this->vent_pattern[$i]);
    }

    protected function next_rock(): Rock {
        switch($this->num_rocks % 5) {
            case 0: return new HorzLine($this->max_height);
            case 1: return new Cross($this->max_height);
            case 2: return new Ell($this->max_height);
            case 3: return new VertLine($this->max_height);
            case 4: return new Square($this->max_height);
        }
    }

    function current_rock(): Rock {
        if (!isset($this->current_rock)) $this->current_rock = $this->next_rock();
        return $this->current_rock;
    }
}

// Test current_rock() picks new next rock
(function(){
    $chamber = new Chamber('');
    assert($first = $chamber->current_rock() instanceof HorzLine, 'new chamber should pick HorzLine');
    assert($first == $chamber->current_rock(), 'rock should stay the same');

    $chamber = new Chamber('', 1);
    assert($chamber->current_rock() instanceof Cross, 'chamber w/ 1 rock should pick Cross');

    $chamber = new Chamber('', 2);
    assert($chamber->current_rock() instanceof Ell, 'chamber w/ 2 rocks should pick Ell');

    $chamber = new Chamber('', 3);
    assert($chamber->current_rock() instanceof VertLine, 'chamber w/ 3 rocks should pick VertLine');

    $chamber = new Chamber('', 4);
    assert($chamber->current_rock() instanceof Square, 'chamber w/ 4 rocks should pick Square');

    $chamber = new Chamber('', 5);
    assert($chamber->current_rock() instanceof HorzLine, 'chamber w/ 5 rocks should pick HorzLine');
    // ...
})();

// Test next_vent()
(function(){
    $chamber = new Chamber('<<>');
    assert($chamber->next_vent() === -1, 'first vent should be to left');
    assert($chamber->next_vent() === -1, 'second vent should be to left');
    assert($chamber->next_vent() === 1, 'third vent should be to right');
    assert($chamber->next_vent() === -1, 'fourth vent should loop around to left');
})();