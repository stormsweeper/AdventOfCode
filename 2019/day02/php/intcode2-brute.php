<?php

$input = trim(file_get_contents($argv[1]));
$input = explode(",", $input);
$input = array_map('intval', $input);

$target_result = intval($argv[2]);

interface IntPuter {
    const OPCODE_ADD = 1;
    const OPCODE_MULTIPLY = 2;
    const OPCODE_EXIT = 99;
    const VALID_OPCODES = [
        IntPuter::OPCODE_ADD,
        IntPuter::OPCODE_MULTIPLY,
        IntPuter::OPCODE_EXIT,
    ];

    function loadProgram(array $program): void;
    function run(): int;
    function getRegister(int $pos): int;
    function setRegister(int $pos, int $val): void;
    function dumpMemory(): array;
    function currentInstruction(): int;
}

class IntPuterV1 implements IntPuter {
    private $registers = [];
    private $cursor = 0;

    function loadProgram(array $program): void {
        $this->registers = $program;
        $this->cursor = 0;
    }

    function run(): int {
        while ($this->cursor < count($this->registers)) {
            @list($opcode, $a, $b, $c) = array_slice($this->registers, $this->cursor, 4);
            if (!in_array($opcode, IntPuter::VALID_OPCODES)) {
                throw new RuntimeException('Unknown opcode: ' . $opcode);
            }
            if ($opcode === IntPuter::OPCODE_EXIT) {
                //echo "exit\n";
                break;
            } elseif ($opcode === IntPuter::OPCODE_ADD) {
                //echo "add reg{$a} ({$this->registers[$a]}) reg{$b} ({$this->registers[$b]}) to reg{$c}\n";
                $a = $this->getRegister($a);
                $b = $this->getRegister($b);
                $this->setRegister($c, $a + $b);
            } elseif ($opcode === IntPuter::OPCODE_MULTIPLY) {
                //echo "multi reg{$a} ({$this->registers[$a]}) reg{$b} ({$this->registers[$b]}) to reg{$c}\n";
                $a = $this->getRegister($a);
                $b = $this->getRegister($b);
                $this->setRegister($c, $a * $b);
            }
            $this->cursor += 4;
        }
        return $this->getRegister(0);
    }

    function getRegister(int $pos): int {
        if (!array_key_exists($pos, $this->registers)) {
            throw new InvalidArgumentException('Invalid position: ' . $pos);
        }
        return $this->registers[$pos];
    }

    function setRegister(int $pos, int $val): void {
        $this->registers[$pos] = $val;
    }

    function dumpMemory(): array {
        return $this->registers;
    }

    function currentInstruction(): int {
        return $this->cursor;
    }
}

$puter = new IntPuterV1;
$found = false;
$max_override = max(99, count($input) - 1);
foreach(range(0, $max_override) as $noun) {
    foreach(range(0, $max_override) as $verb) {
        $puter->loadProgram($input);
        $puter->setRegister(1, $noun);
        $puter->setRegister(2, $verb);
        try {
            $result = $puter->run();
            //echo "result: {$result} / noun: {$noun} / verb: {$verb}\n";
            if ($result === $target_result) {
                $found = true;
                break 2;
            }
        } catch (Exception $e) {
            
        }
    }
}

echo "found: {$found} / noun: {$noun} / verb: {$verb}\n";
if ($found) {
    $result = $noun * 100 + $verb;
    echo "result: {$result}\n";
}