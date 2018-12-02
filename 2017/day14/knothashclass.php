<?php

class KnotHash
{
    const LENGTH = 256;
    const COMPACT_LENGTH = 16;
    private static $salt = [17, 31, 73, 47, 23];
    private $hash;
    private $input;
    private $compacthash;
    private $hex;
    private $bin;

    public function __construct(string $input) {
        $this->input = $input;
    }

    private function calculateHash() {
        if (isset($this->hash)) {
            return;
        }

        $sequence = [];
        if (strlen($this->input)) {
            $sequence = array_map('ord', str_split($this->input));
        }
        $sequence = array_merge($sequence, self::$salt);

        $hash = range(0, 255);
        $skip = 0;
        $pointer = 0;
        $runs = 64;
        while ($runs-- > 0) {
            foreach ($sequence as $step) {
                $this->twist($hash, $step, $pointer);
                $pointer = ($pointer + $step + $skip) % self::LENGTH;
                $skip++;
            }
        }

        $this->hash = $hash;

        $compact = [];
        for ($i = 0; $i < self::COMPACT_LENGTH; $i++) {
            $slice = array_slice($hash, $i * self::COMPACT_LENGTH, self::COMPACT_LENGTH);
            $xored = array_reduce(
                $slice,
                function($carry, $item) {
                    return $carry ^ $item;
                },
                0
            );
            $compact[$i] = $xored;
        }
        $this->compacthash = $compact;
    }
    
    private function twist(array &$hash, int $step, int $pointer) {
        $indices = array_map(
            function($index) {
                return $index % self::LENGTH;
            },
            range($pointer, $pointer + $step - 1)
        );
    
        $values = array_map(
            function($index) use ($hash) {
                return $hash[$index];
            },
            array_reverse($indices)
        );
    
        foreach ($indices as $vindex => $hindex) {
            $hash[$hindex] = $values[$vindex];
        }
    }

    public function fullHash(): array {
        $this->calculateHash();
        return $this->hash;
    }

    public function compactHash(): array {
        $this->calculateHash();
        return $this->compacthash;
    }

    public function toHex(): string {
        if (isset($this->hex)) {
            return $this->hex;
        }

        $hex = array_map(
            function($xored) {
                return str_pad(dechex($xored), 2, '0', STR_PAD_LEFT);
            },
            $this->compactHash()
        );
        return $this->hex = implode('', $hex);
    }

    public function toBin(): string {
        if (isset($this->bin)) {
            return $this->bin;
        }

        $bin = array_map(
            function($xored) {
                return str_pad(decbin($xored), 8, '0', STR_PAD_LEFT);
            },
            $this->compactHash()
        );
        return $this->bin = implode('', $bin);
    }
}