<?php

class SubALU {
    private array $registers = [];
    private int $cursor = 0;
    private array $inputs = [];

    public function __construct(array $inputs = []) { $this->reset($inputs); }

    public function run(array $program, bool $debugmode = false): void {
        
        foreach ($program as $pline) {
            [$line, $op, $r, $b] = array_pad($pline, 4, '');
            if ($debugmode) echo "{$line}\n";
            // yay PHP callables
            [$this, $op]($r, $b);
            if ($debugmode) echo json_encode($this->memdump()) . "\n";
        }
    }

    // util functions
    public function reset(?array $inputs) {
        $this->registers = array_fill_keys(range('w','z'), 0);
        $this->cursor = 0;
        if (isset($inputs)) $this->inputs = $inputs;
    }

    public function read(string $r): int { 
        return $this->registers[$r]??0;
    }

    public function write(string $r,  int $v): void {
        $this->registers[$r] = $v;
    }

    public function memdump(): array {
        return $this->registers;
    }

    // actual program functions
    public function inp(string $r): void {
        $this->write($r, (int)$this->inputs[$this->cursor++]);
    }

    public function add(string $r, string|int $b): void {
        $a = $this->read($r);
        if (!is_numeric($b)) $b = $this->read($b);
        $this->write($r, $a+$b);
    }

    public function mul(string $r, string|int $b): void {
        $a = $this->read($r);
        if (!is_numeric($b)) $b = $this->read($b);
        $this->write($r, $a*$b);
    }

    public function div(string $r, string|int $b): void {
        $a = $this->read($r);
        if (!is_numeric($b)) $b = $this->read($b);
        $this->write($r, $a/$b);
    }

    public function mod(string $r, string|int $b): void {
        $a = $this->read($r);
        if (!is_numeric($b)) $b = $this->read($b);
        $this->write($r, $a%$b);
    }

    public function eql(string $r, string|int $b): void {
        $a = $this->read($r);
        if (!is_numeric($b)) $b = $this->read($b);
        $this->write($r, $a===(int)$b?1:0);
    }
}