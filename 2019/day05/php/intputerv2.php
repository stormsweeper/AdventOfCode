<?php

class IntPuterV2 {
    const MAX_RAW_OPCODE = 11199;

    const OPCODE_ADD = 1;
    const OPCODE_MULTIPLY = 2;
    const OPCODE_INPUT = 3;
    const OPCODE_OUTPUT = 4;
    const OPCODE_EXIT = 99;
    const OPCODE_INVALID = -1;
    const VALID_OPCODES = [
        self::OPCODE_ADD,
        self::OPCODE_MULTIPLY,
        self::OPCODE_INPUT,
        self::OPCODE_OUTPUT,
        self::OPCODE_EXIT,
    ];

    const OPCODE_PARAM_LENGTHS = [
        self::OPCODE_ADD => 3,
        self::OPCODE_MULTIPLY => 3,
        self::OPCODE_INPUT => 3,
        self::OPCODE_OUTPUT => 3,
        self::OPCODE_EXIT => 0,
    ];

    const PARAM_MODE_POS = 0;
    const PARAM_MODE_IMM = 1;

    private $registers = [];
    private $cursor = 0;

    function loadProgram(array $program): void {
        $this->registers = $program;
    }

    function run(): void {
        $this->cursor = 0;
        while ($this->cursor < count($this->registers)) {
            list($opcode, $a_mode, $b_mode, $c_mode) = $this->parseOpcodeAndModes($this->readNextRegister());
            if ($opcode === self::OPCODE_INVALID) {
                throw new RuntimeException('Unknown opcode: ' . $opcode);
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
            }
        }
    }

    function parseOpcodeAndModes(int $rawopcode): array {
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
        $asint = null;
        while (!isset($asint)) {
            $input = trim(readline('? '));
            $asint = (int)$input;
            if ((string)$asint !== $input) {
                $asint = null;
            }
        }
        return $asint;
    }

    function output(int $value): void {
        echo $value . "\n";
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

    function currentInstruction(): int {
        return $this->cursor;
    }

}