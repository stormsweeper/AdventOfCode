<?php

class Prog
{
    private $progid;
    private $instp = 0;
    private $instructions;
    private $blocked = false;
    public $registers = [];
    private $counts = [];

    function __construct($progid, $instructions) {
        $this->progid = $progid;
        //$this->registers['p'] = $progid;
        $this->instructions = $instructions;
    }

    function instructionCounts() {
        return $this->counts;
    }

    function isBlocked() {
        return $this->blocked;
    }

    function isTerminated() {
        return $this->instp >= count($this->instructions);
    }

    function nextInstruction() {
        if ($this->isBlocked() || $this->isTerminated()) {
            return;
        }

        // parse
        $instp = $this->instp;
        $instruction = $this->instructions[$instp];
        echo "prog {$this->progid} {$instruction}\n";
        $args = explode(' ', $instruction);
        $cmd = array_shift($args);
        call_user_func_array([$this, $cmd], $args);
        $this->counts[$cmd] = ($this->counts[$cmd] ?? 0) + 1;

        if (!$this->isBlocked() && $instp === $this->instp) {
            $this->instp++;
        }
    }

    function read($reg) {
        if (is_numeric($reg)) {
            return $reg;
        }
        return $this->registers[$reg] ?? 0;
    }

    function set($reg, $val) {
        return $this->registers[$reg] = $this->read($val);
    }

    function sub($reg, $add) {
        $this->set($reg,  $this->read($reg) - $this->read($add));
    }

    function mul($reg, $mul) {
        $this->set($reg,  $this->read($reg) * $this->read($mul));
    }

    function jnz($reg, $jump) {
        if ($this->read($reg) <> 0) {
            $this->instp +=  $this->read($jump);
        }
    }
}
