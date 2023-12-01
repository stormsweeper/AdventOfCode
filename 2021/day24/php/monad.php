<?php

require_once 'subalu.php';

function parse_program(string $code): array {
    $regex = '/(inp|add|mul|div|mod|eql) ([w-z])(?: ([w-z]|-?\d+))?/s';
    preg_match_all($regex, $code, $matches, PREG_SET_ORDER);
    return $matches;
}

function test_alu(): void {
    $alu = new SubALU();

    // For example, here is an ALU program which takes an input number, negates
    // it, and stores it in x:
    $test1 = parse_program('inp x
    mul x -1');
    foreach ([0=>0, 1=>-1, -2=>2] as $input => $expected) {
        $alu->reset([$input]);
        $alu->run($test1);
        assert($alu->read('x') === $expected, "test1([{$input}]) === {$expected}?");
    }

    // Here is an ALU program which takes two input numbers, then sets z to 1
    // if the second input number is three times larger than the first input
    // number, or sets z to 0 otherwise:
    $test2 = parse_program('inp z
    inp x
    mul z 3
    eql z x');
    $stubs = [
        // a,b, t/f
        [0,0, 1],
        [0,3, 0],
        [3,0, 0],
        [3,1, 0],
        [1,3, 1],
        [1,6, 0],
        [17,51, 1],
    ];
    foreach ($stubs as [$a, $b, $expected]) {
        $alu->reset([$a, $b]);
        $alu->run($test2);
        assert($alu->read('z') === $expected, "test2([{$a}, {$b}]) === {$expected}?");
    }

    // Here is an ALU program which takes a non-negative integer as input,
    // converts it into binary, and stores the lowest (1's) bit in z, the
    // second-lowest (2's) bit in y, the third-lowest (4's) bit in x, and the
    // fourth-lowest (8's) bit in w:
    $test3 = parse_program('inp w
    add z w
    mod z 2
    div w 2
    add y w
    mod y 2
    div w 2
    add x w
    mod x 2
    div w 2
    mod w 2');
    $stubs = [
        // input, last 4 bits
        [0, 0,0,0,0],
        [1, 0,0,0,1],
        [2, 0,0,1,0],
        [7, 0,1,1,1],
        [13, 1,1,0,1],
        [16, 0,0,0,0],
        [42, 1,0,1,0],
    ];
    foreach ($stubs as [$input, $w, $x, $y, $z]) {
        $alu->reset([$input]);
        $alu->run($test3);
        assert(
            $alu->read('w') === $w
            &&
            $alu->read('x') === $x
            &&
            $alu->read('y') === $y
            &&
            $alu->read('z') === $z
            , "test3([{$input}]) === [{$w}, {$x}, {$y}, {$z}]"
        );
    }

}

if (!isset($argv[1])) {
    echo "Testing ALU...\n";
    test_alu();
    echo "All tests passed!\n";
    exit;
}

$program = trim(file_get_contents($argv[1]));
$program = parse_program($program);

$modelnum = 99999753283815;

$alu = new SubALU(str_split($modelnum));
$alu->run($program, false);
print_r($alu->memdump());

