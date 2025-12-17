<?php

$shematic = explode("\n", trim(file_get_contents($argv[1])));

abstract class Component {
    static function parse(string $def): Component {
        [$label, $outputs] = explode(' -> ', $def);
        $outputs = explode(', ', $outputs);
        if ($label[0] === '%') return new FlipFlop(substr($label, 1), $outputs);
        if ($label[0] === '&') return new NAND(substr($label, 1), $outputs);
        return new Passthru($label, $outputs);
    }

    function __construct(readonly string $name, protected array $outputs) {}

    function outputs(): array { return $this->outputs; }

    abstract function outpulse(): int;

    function set(string $input, int $pulse): void {
        $this->inputs[$input] = $pulse;
    }

    function read(): array {
        $pulse = $this->outpulse();
        return array_map(
            function($output) use ($pulse) {
                return [$pulse, $output, $this->name];
            },
            $this->outputs
        );
    }
}

class FlipFlop extends Component {
    private bool $on = false;
    private bool $changed = false;
    function outpulse(): int { return (int)$this->on; }
    function set(string $input, int $pulse): void {
        if ($pulse === 0) {
            $this->changed = true;
            $this->on = !$this->on;
        }
    }
    function read(): array {
        if ($this->changed) {
            $this->changed = false;
            return parent::read();
        }
        return [];
    }
}

class NAND extends Component {
    private array $inputs = [];
    function set(string $input, int $pulse): void {
        $this->inputs[$input] = $pulse;
    }
    function outpulse(): int {
        $in = array_reduce(
            $this->inputs,
            function($carry, $i): int { return $carry & $i; },
            1
        );
        return $in === 1 ? 0 : 1;
    }
}

class Passthru extends Component {
    private int $in = 0;
    function outpulse(): int { return $this->in; }
    function set(string $input, int $pulse): void { $this->in = $pulse; }
}

class LowCounter extends Passthru {
    private int $count = 0;
    function set(string $input, int $pulse): void { 
        parent::set($input, $pulse);
        if ($pulse === 0) $this->count++;
    }

    function count(): int { return $this->count; }
}

$circuit = [];
// parse components
foreach ($shematic as $def) {
    $comp = Component::parse($def);
    $circuit[$comp->name] = $comp;
}
$circuit['rx'] = new LowCounter('rx', []);
$on_at = -1;

// set NAND inputs to low
foreach (array_keys($circuit) as $cname) {
    foreach ($circuit[$cname]->outputs() as $o) {
        if ($o === 'rx') $on_at = 0;
        $target = $circuit[$o] ?? null;
        if ($target instanceof NAND) $target->set($cname, 0);
    }
}

$lows = $highs = 0;

for ($c = 1; $c <= 1000; $c++) {
    // echo "cycle {$c}\n";
    $cycle = [ [0, 'broadcaster', 'button'] ];
    while ($cycle) {
        [$pulse, $target, $input] = array_shift($cycle);
        // echo "[$pulse, $target, $input]\n";
        if ($pulse === 0) $lows++;
        if ($pulse === 1) $highs++;
        $comp = $circuit[$target] ?? null;
        if (!isset($comp)) continue;
        $comp->set($input, $pulse);
        $cycle = array_merge($cycle, $comp->read());
    }
    if (!$on_at && $circuit['rx']->outpulse() === 0) {
        $on_at = $c;
    }
}

$p1 = $lows * $highs;

for (; $c < 10000000; $c++) {
    $cycle = [ [0, 'broadcaster', 'button'] ];
    while ($cycle) {
        [$pulse, $target, $input] = array_shift($cycle);
        // echo "[$pulse, $target, $input]\n";
        $comp = $circuit[$target] ?? null;
        if (!isset($comp)) continue;
        $comp->set($input, $pulse);
        $cycle = array_merge($cycle, $comp->read());
    }
    if (!$on_at && $circuit['rx']->outpulse() === 0) {
        $on_at = $c;
    }

}
// while (!$on_at) {
//     $c++;
//     // if ($c % 1000 === 0) echo "cycle: {$c}\n";
//     $cycle = [ [0, 'broadcaster', 'button'] ];
//     while ($cycle) {
//         [$pulse, $target, $input] = array_shift($cycle);
//         // echo "[$pulse, $target, $input]\n";
//         $comp = $circuit[$target] ?? null;
//         if (!isset($comp)) continue;
//         $comp->set($input, $pulse);
//         $cycle = array_merge($cycle, $comp->read());
//     }
//     if (!$on_at && $circuit['rx']->outpulse() === 0) {
//         $on_at = $c;
//     }
// }

echo "p1: {$lows} * {$highs} = {$p1}\np2: {$on_at}\n";
print_r($circuit['rx']);