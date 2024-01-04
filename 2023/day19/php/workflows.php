<?php

class Workflow {
    private static $workflows = [];
    static function get(string $name): Workflow { return self::$workflows[$name]; }
    static function add(string $name, Workflow $workflow): void { self::$workflows[$name] = $workflow; }
    static function remove(string $name): void { unset(self::$workflows[$name]); }
    static function parseAndAdd(string $wdef): void {
        [$name, $rules] = explode('{', $wdef);
        $rules = explode(',', substr($rules, 0, -1));
        self::$workflows[$name] = new Workflow($rules);
    }
    private function __construct(private array $rules) {}
    function accepts(array $part): bool {
        $next = null;
        foreach ($this->rules as &$rule) {
            // shortcut these
            if ($rule === 'A') return true;
            if ($rule === 'R') return false;
            if (is_string($rule)) {
                // also can shortcut this
                if (strpos($rule, ':') === false) return Workflow::get($rule)->accepts($part);
                $rule = Rule::parse($rule);
            }
            $result = $rule->applyTo($part);
            if (is_bool($result)) return $result;
            if ($result instanceof Workflow) return $result->accepts($part);
        }
        return false;
    }
}

abstract class Rule {
    static function parse(string $def): Rule {
        if ($def === 'A') return AcceptRule::get();
        if ($def === 'R') return RejectRule::get();
        if (strpos($def, '<') !== false) return LTRule::get($def);
        if (strpos($def, '>') !== false) return GTRule::get($def);
        return FwdRule::get($def);
    }

    abstract function applyTo(array $part): null|bool|Workflow;
}

class AcceptRule extends Rule {
    static function get(): AcceptRule {
        static $instance = new AcceptRule();
        return $instance;
    }
    private function __construct() {}
    function applyTo(array $part): bool { return true; } 
}

class RejectRule extends Rule { 
    static function get(): RejectRule {
        static $instance = new RejectRule();
        return $instance;
    }
    private function __construct() {}
    function applyTo(array $part): bool { return false; } 
}

class FwdRule extends Rule {
    static function get(string $name): FwdRule {
        static $fwds = [];
        if (!isset($fwds[$name])) $fwds[$name] = new FwdRule($name);
        return $fwds[$name];
    }
    private function __construct(private string $name) {}
    function applyTo(array $part): Workflow { return Workflow::get($this->name); }
}

class LTRule extends Rule {
    private string $quality;
    private int $compare;
    private string $target;

    static function get(string $def): LTRule {
        static $rules = [];
        if (!isset($rules[$def])) $rules[$def] = new LTRule($def);
        return $rules[$def];
    }
    private function __construct(string $def) {
        [$op, $this->target] = explode(':', $def);
        [$this->quality, $compare] = explode('<', $op);
        $this->compare = intval($compare);
    }

    function applyTo(array $part): null|bool|Workflow {
        if ($part[$this->quality] >= $this->compare) return null;
        if ($this->target === 'A') return true;
        if ($this->target === 'R') return false;
        return Workflow::get($this->target);
    }
}

class GTRule extends Rule {
    private string $quality;
    private int $compare;
    private string $target;

    static function get(string $def): GTRule {
        static $rules = [];
        if (!isset($rules[$def])) $rules[$def] = new GTRule($def);
        return $rules[$def];
    }
    private function __construct(string $def) {
        [$op, $this->target] = explode(':', $def);
        [$this->quality, $compare] = explode('>', $op);
        $this->compare = intval($compare);
    }

    function applyTo(array $part): null|bool|Workflow {
        if ($part[$this->quality] <= $this->compare) return null;
        if ($this->target === 'A') return true;
        if ($this->target === 'R') return false;
        return Workflow::get($this->target);
    }
}
