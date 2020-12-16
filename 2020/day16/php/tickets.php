<?php
$input = trim(file_get_contents($argv[1]));

list($rulestext, $mine, $nearby) = explode("\n\n", $input);

preg_match_all(
    '#(?<field>[\w ]+): (?<min_low>\d+)-(?<max_low>\d+) or (?<min_high>\d+)-(?<max_high>\d+)#',
    $rulestext,
    $rules,
    PREG_SET_ORDER
);
$rules = array_map(
    function($r) {return array_filter($r, 'is_string', ARRAY_FILTER_USE_KEY);},
    $rules
);

function parse_ticket(string $ticket): array {
    return array_map('intval', explode(',', $ticket));
}

function validate_ticket($ticket): int {
    global $rules;
    $invalid = 0;
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
    $r = json_encode($rule);
    echo "val: {$val} valid: {$valid} rule: {$r}\n";
    return $valid;
}

list(, $mine) = explode(":\n", $mine);
list(, $nearby) = explode(":\n", $nearby);
$nearby = explode("\n", $nearby);

$p1 = array_sum(array_map('validate_ticket', $nearby));
# print_r($rules);
echo "Part 1: {$p1}\n";