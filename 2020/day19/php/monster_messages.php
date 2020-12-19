<?php

$inputs = trim(file_get_contents($argv[1]));
list($unparsed_raw, $messages) = explode("\n\n", $inputs, 2);

preg_match_all('#^(\d+): (.+)$#m', $unparsed_raw, $matches);
$unparsed = array_combine($matches[1], $matches[2]);


function parse_rule(string $rule, array $decoded): ?string {
    if (strpos($rule, '"') !== false) return substr($rule, 1, 1);
    $rule = preg_replace_callback(
        '#\d+#',
        function($m) use ($decoded) {
            return $decoded[$m[0]] ?? $m[0];
        },
        $rule
    );
    if (preg_match('#\d#', $rule)) return null;
    $rule = str_replace(' ', '', $rule);
    if (strpos($rule, '|') !== false) $rule = "(?:{$rule})";
    return $rule;
}

function generate_regex(array $unparsed): string {

    uasort(
        $unparsed,
        function($a, $b) {
            # strs first
            $a_str = strpos($a, '"') !== false;
            $b_str = strpos($b, '"') !== false;
            if ($a_str && $b_str) return $a <=> $b;
            if ($a_str) return -1;
            if ($b_str) return 1;
            # otherwise just compare len
            return strlen($a) <=> strlen($b) ?: $a <=> $b;
        }
    );

    $decoded = [];
    
    while (!isset($decoded[0])) {
        $next = $unparsed;
        foreach ($unparsed as $rn => $rule) {
            $parsed =  parse_rule($rule, $decoded);
            if (isset($parsed)) {
                $decoded[$rn] = $parsed;
                unset($next[$rn]);
            }
        }
        $unparsed = $next;
    }
    
    return "#^{$decoded[0]}$#m";
}

$p1_regex = generate_regex($unparsed);
$p1 = preg_match_all($p1_regex, $messages);

echo "Part 1: {$p1}\n";