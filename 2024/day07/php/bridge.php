<?php

class Equation {

    static function parse(string $eqstr): self {
        [$res, $ops] = explode(': ', $eqstr);
        $ops = array_map('intval', explode(' ', $ops));
        return new self(intval($res), $ops);
    }

    function __construct(public int $result, public array $operands) {}

    function canSolveAddMul(): bool {
        $b = $this->operands;
        $a = array_shift($b);
        return $this->bruteAddMul($a, $b);
    }

    function bruteAddMul(int $a, int|array $b): bool {
        if (is_array($b)) {
            if (count($b) === 1) {
                $b = $b[0];
            } else {
                $x = array_shift($b);
                return $this->bruteAddMul($a + $x, $b) || $this->bruteAddMul($a * $x, $b);
            }
        }

        return $this->result === $a + $b || $this->result === $a * $b;
    }

    function canSolveAddMulCon(): bool {
        $b = $this->operands;
        $a = array_shift($b);
        return $this->bruteAddMulCon($a, $b);
    }

    function bruteAddMulCon(int $a, int|array $b): bool {
        if (is_array($b)) {
            if (count($b) === 1) {
                $b = $b[0];
            } else {
                $x = array_shift($b);
                return $this->bruteAddMulCon($a + $x, $b) || $this->bruteAddMulCon($a * $x, $b) || $this->bruteAddMulCon(intval($a . $x), $b);
            }
        }

        return $this->result === $a + $b || $this->result === $a * $b || $this->result === intval($a . $b);
    }

}

$equations = array_map(
    'Equation::parse',
    explode("\n", trim(file_get_contents($argv[1])))
);

$p1 = $p2 = 0;

foreach ($equations as $eq) {
    if ($eq->canSolveAddMul()) $p1 += $eq->result;
    if ($eq->canSolveAddMulCon()) $p2 += $eq->result;
}

echo "p1: {$p1}\n";
echo "p2: {$p2}\n";
