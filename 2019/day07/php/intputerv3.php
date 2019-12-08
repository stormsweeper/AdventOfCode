<?php

class IntPuterV3 {
    const MAX_RAW_OPCODE = 11199;

    const OPCODE_ADD = 1;
    const OPCODE_MULTIPLY = 2;
    const OPCODE_INPUT = 3;
    const OPCODE_OUTPUT = 4;
    const OPCODE_JUMP_IF_TRUE = 5;
    const OPCODE_JUMP_IF_FALSE = 6;
    const OPCODE_LESS_THAN = 7;
    const OPCODE_EQUALS = 8;
    const OPCODE_EXIT = 99;
    const OPCODE_INVALID = -1;
    const VALID_OPCODES = [
        self::OPCODE_ADD,
        self::OPCODE_MULTIPLY,
        self::OPCODE_INPUT,
        self::OPCODE_OUTPUT,
        self::OPCODE_JUMP_IF_TRUE,
        self::OPCODE_JUMP_IF_FALSE,
        self::OPCODE_LESS_THAN,
        self::OPCODE_EQUALS,
        self::OPCODE_EXIT,
    ];

    const PARAM_MODE_POS = 0;
    const PARAM_MODE_IMM = 1;

    private $registers = [];
    private $cursor = 0;
    private $inputs = [];
    private $last_output;

    function loadProgram(array $program): void {
        $this->registers = $program;
    }

    function run(): void {
        $this->cursor = 0;
        while ($this->cursor < count($this->registers)) {
            $rawopcode = $this->readNextRegister();
            list($opcode, $a_mode, $b_mode, $c_mode) = $this->parseOpcodeAndModes($rawopcode);
            if ($opcode === self::OPCODE_INVALID) {
                throw new RuntimeException('Unknown opcode: ' . $rawopcode);
            }
            if ($opcode === self::OPCODE_EXIT) {
                break;
            } elseif ($opcode === self::OPCODE_ADD) {
                $a = $this->readNextRegister();
                if ($a_mode === self::PARAM_MODE_POS) {
                    $a = $this->getRegister($a);
                }
                $b = $this->readNextRegister();
                if ($b_mode === self::PARAM_MODE_POS) {
                    $b = $this->getRegister($b);
                }
                $c = $this->readNextRegister();
                $this->setRegister($c, $a + $b);
            } elseif ($opcode === self::OPCODE_MULTIPLY) {
                $a = $this->readNextRegister();
                if ($a_mode === self::PARAM_MODE_POS) {
                    $a = $this->getRegister($a);
                }
                $b = $this->readNextRegister();
                if ($b_mode === self::PARAM_MODE_POS) {
                    $b = $this->getRegister($b);
                }
                $c = $this->readNextRegister();
                $this->setRegister($c, $a * $b);
            } elseif ($opcode === self::OPCODE_INPUT) {
                $a = $this->readNextRegister();
                $this->setRegister($a, $this->getUserInput());
            } elseif ($opcode === self::OPCODE_OUTPUT) {
                $a = $this->readNextRegister();
                if ($a_mode === self::PARAM_MODE_POS) {
                    $a = $this->getRegister($a);
                }
                $this->output($a);
            } elseif ($opcode === self::OPCODE_JUMP_IF_TRUE) {
                $a = $this->readNextRegister();
                if ($a_mode === self::PARAM_MODE_POS) {
                    $a = $this->getRegister($a);
                }
                $b = $this->readNextRegister();
                if ($b_mode === self::PARAM_MODE_POS) {
                    $b = $this->getRegister($b);
                }
                if ($a !== 0) {
                    $this->jumpTo($b);
                }
            } elseif ($opcode === self::OPCODE_JUMP_IF_FALSE) {
                $a = $this->readNextRegister();
                if ($a_mode === self::PARAM_MODE_POS) {
                    $a = $this->getRegister($a);
                }
                $b = $this->readNextRegister();
                if ($b_mode === self::PARAM_MODE_POS) {
                    $b = $this->getRegister($b);
                }
                if ($a === 0) {
                    $this->jumpTo($b);
                }
            } elseif ($opcode === self::OPCODE_LESS_THAN) {
                $a = $this->readNextRegister();
                if ($a_mode === self::PARAM_MODE_POS) {
                    $a = $this->getRegister($a);
                }
                $b = $this->readNextRegister();
                if ($b_mode === self::PARAM_MODE_POS) {
                    $b = $this->getRegister($b);
                }
                $c = $this->readNextRegister();
                $this->setRegister($c, (int)$a < $b);
            } elseif ($opcode === self::OPCODE_EQUALS) {
                $a = $this->readNextRegister();
                if ($a_mode === self::PARAM_MODE_POS) {
                    $a = $this->getRegister($a);
                }
                $b = $this->readNextRegister();
                if ($b_mode === self::PARAM_MODE_POS) {
                    $b = $this->getRegister($b);
                }
                $c = $this->readNextRegister();
                $this->setRegister($c, (int)$a === $b);
            }
        }
    }

    function parseOpcodeAndModes(int $rawopcode): array {
        // maybe not required, but strict reading of the format suggests it only looks at digits
        $rawopcode = abs($rawopcode);
        $opcode = $rawopcode % 100;
        if ($rawopcode > self::MAX_RAW_OPCODE || !in_array($opcode, self::VALID_OPCODES)) {
            return [self::OPCODE_INVALID, false, false, false];
        }
        $modes = $rawopcode - $opcode;
        $c_mode = intval($modes / 10000); // floor returns a float
        $modes %= 10000;
        $b_mode = intval($modes / 1000);
        $modes %= 1000;
        $a_mode = intval($modes / 100);
        return [$opcode, $a_mode, $b_mode, $c_mode];
    }

    function getUserInput(): int {
        if (!$this->inputs) {
            throw new RuntimeException('No queued input');
        }
        return array_shift($this->inputs);
    }

    function queueInput(int $input): void {
        $this->inputs[] = $input;
    }

    function clearInputs(): void {
        $this->inputs = [];
    }

    function output(int $value): void {
        $this->last_output = $value;
    }

    function lastOutput(): int {
        if (!isset($this->last_output)) {
            throw new RuntimeException('No output');
        }
        return $this->last_output;
    }

    function clearOutput(): void {
        $this->last_output = null;
    }

    function readNextRegister(): int {
        return $this->getRegister($this->cursor++);
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

    function jumpTo(int $pos): void {
        $this->cursor = $pos;
    }

    function currentInstruction(): int {
        return $this->cursor;
    }

}