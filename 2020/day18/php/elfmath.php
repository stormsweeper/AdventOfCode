<?php
$inputs = trim(file_get_contents($argv[1]));
$inputs =explode("\n", $inputs);

function solve_eq(string $eq, bool $advanced = false): int {
    $eq = simplify_eq($eq, $advanced);
    if ($advanced) {
        while (strpos($eq, '+') !== false) {
            $eq = preg_replace_callback(
                '#\d+ [\+\d]+ \d+#',
                function($m) {
                    return solve_eq($m[0]);
                },
                $eq            
            );
        }
    }
    $args = explode(' ', $eq);
    $len = count($args);
    $carry = intval($args[0]);
    for ($i = 1; $i < $len - 1; $i += 2) {
        $operator = $args[$i];
        $operand  = intval($args[$i + 1]);
        if ($operator === '+') $carry += $operand;
        if ($operator === '*') $carry *= $operand;
    }
    return $carry;
}

function simplify_eq(string $eq, bool $advanced = false): string {
    while (strpos($eq, '(') !== false) {
        $eq = preg_replace_callback(
            '#\(([^()]+)\)#',
            function($m) use ($advanced) {
                return solve_eq($m[1], $advanced);
            },
            $eq
        );
    }
    return $eq;
}

assert(solve_eq('1 + 2 * 3 + 4 * 5 + 6') === 71, 'Example 1: no parens');
assert(solve_eq('1 + 2 * 3 + 4 * 5 + 6', true) === 231, 'Example 1: no parens');
assert(solve_eq('1 + (2 * 3) + (4 * (5 + 6))') === 51, 'Example 2: parens');
assert(solve_eq('1 + (2 * 3) + (4 * (5 + 6))', true) === 51, 'Example 2: parens');
assert(solve_eq('2 * 3 + (4 * 5)', true) === 46);
assert(solve_eq('5 * 9 * (7 * 3 * 3 + 9 * 3 + (8 + 6 * 4))', true) === 669060);
assert(solve_eq('((2 + 4 * 9) * (6 + 9 * 8 + 6) + 6) + 2 + 4 * 2', true) === 23340);

$p1 = array_sum(array_map('solve_eq', $inputs));
echo "Part 1: {$p1}\n";

$p2 = array_sum(array_map(function($i) {return solve_eq($i, true);}, $inputs));
echo "Part 2: {$p2}\n";
