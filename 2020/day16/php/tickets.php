<?php
$input = trim(file_get_contents($argv[1]));

list($rulestext, $mine, $nearby) = explode("\n\n", $input);

preg_match_all(
    '#(?<field>[\w ]+): (?<min_low>\d+)-(?<max_low>\d+) or (?<min_high>\d+)-(?<max_high>\d+)#',
    $rulestext,
    $matches,
    PREG_SET_ORDER
);
$rules = []; $dep_rules = [];
foreach ($matches as $m) {
    $r =  array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
    $rules[$m['field']] = $r;
    if (strpos($m['field'], 'departure') !== false) $dep_rules[$m['field']] = $r;
}

function parse_ticket(string $ticket): array {
    return array_map('intval', explode(',', $ticket));
}

function validate_ticket($ticket): ?int {
    global $rules;
    $invalid = null;
    if (is_string($ticket)) $ticket = parse_ticket($ticket);
    foreach ($ticket as $i => $val) {
        foreach ($rules as $rule) {
            if (
                ($val >= $rule['min_low'] && $val <= $rule['max_low'])
                ||
                ($val >= $rule['min_high'] && $val <= $rule['max_high'])
            ) {
                continue 2;
            }
        }
        $invalid += $val;
    }
    return $invalid;
}

function meets_rule(int $val, array $rule): bool {
    $valid = false;
    if (
        ($val >= $rule['min_low'] && $val <= $rule['max_low'])
        ||
        ($val >= $rule['min_high'] && $val <= $rule['max_high'])
    ) {
        $valid = true;
    }
    return $valid;
}

list(, $mine) = explode(":\n", $mine);
list(, $nearby) = explode(":\n", $nearby);
$nearby = explode("\n", $nearby);

$p1 = array_sum(array_map('validate_ticket', $nearby));
# print_r($rules);
echo "Part 1: {$p1}\n";

$len = count($rules);
$valid = array_filter($nearby, function($n) { return validate_ticket($n) === null; });
$possible = array_fill(0, $len, array_keys($rules));
$definite = [];

while (count($definite) < $len) {
    $t = array_pop($valid);
    foreach (parse_ticket($t) as $i => $v) {
        if (isset($definite[$i])) continue;
        $not = [];
        foreach ($possible[$i] as $rk) {
            if (!meets_rule($v, $rules[$rk])) $not[] = $rk;
        }
        $possible[$i] = array_values(array_diff($possible[$i], $not));
    }
    do {
        $changed = false;
        foreach ($possible as $i => $p) {
            if (count($p) === 1) {
                $definite[$i] = $p[0];
                $changed = true;
            }
        }
        if ($changed) {
            $possible = array_map(
                function ($p) use ($definite) {
                    return array_values(array_diff($p, $definite));
                },
                array_diff_key($possible, $definite)
            );
        }
    } while ($changed);
}

$p2 = 1;
$mine = parse_ticket($mine);

foreach ($definite as $i => $rk) {
    if (strpos($rk, 'departure') === 0) $p2 *= $mine[$i];
}

echo "Part 2: {$p2}\n"; 
