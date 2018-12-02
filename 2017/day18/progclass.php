<?php

class Prog
{
    private $progid;
    private $instp = 0;
    private $instructions;
    private $blocked = false;
    private $registers = [];
    private $rcvqueue = [];
    private $sent = 0;
    protected $partner;

    function __construct($progid, $instructions) {
        $this->progid = $progid;
        $this->registers['p'] = $progid;
        $this->instructions = $instructions;
    }

    function setPartner(Prog $partner) {
        $this->partner = $partner;
        $partner->partner = $this;
    }

    function queue($val) {
        $this->rcvqueue[] = $val;
        $this->blocked = false;
    }

    function numSent() {
        return $this->sent;
    }

    function isBlocked() {
        return $this->blocked;
    }

    function isTerminated() {
        if ($this->isBlocked()) {
            return  $this->partner->isBlocked() || $this->partner->isTerminated();
        }
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
    
    function snd($val) {
        $this->partner->queue($this->read($val));
        $this->sent++;
    }
    
    function set($reg, $val) {
        return $this->registers[$reg] =  $this->read($val);
    }
    
    function add($reg, $add) {
        $this->set($reg,  $this->read($reg) +  $this->read($add));
    }
    
    function mul($reg, $mul) {
        $this->set($reg,  $this->read($reg) *  $this->read($mul));
    }
    
    function mod($reg, $mod) {
        $this->set($reg,  $this->read($reg) %  $this->read($mod));
    }
    
    function rcv($reg) {
        if (!$this->rcvqueue) {
            $this->blocked = true;
        } else {
            $this->set($reg, array_shift($this->rcvqueue));
        }
    }
    
    function jgz($reg, $jump) {
        if ($this->read($reg) > 0) {
            $this->instp +=  $this->read($jump);
        }
    }
}
