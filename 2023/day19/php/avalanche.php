<?php

require_once 'workflows.php';

function parse_part(string $pdef) {
    $pdef = strtr(
        $pdef,
        [
            '=' => ':',
            'x' => '"x"',
            'm' => '"m"',
            'a' => '"a"',
            's' => '"s"',
        ]
    );
    return json_decode($pdef, true);
}

[$workflow_defs, $part_defs] = explode("\n\n", trim(file_get_contents($argv[1])));

// load workflows
foreach (explode("\n", $workflow_defs) as $wdef) Workflow::parseAndAdd($wdef);

$parts = array_map('parse_part', explode("\n", $part_defs));

$p1 = 0;

foreach ($parts as $part) {
    if (Workflow::get('in')->accepts($part)) $p1 += array_sum($part);
}

echo "p1: {$p1}\n";

$p2 = 0;
foreach (range(1, 4000) as $x) {
    foreach (range(1, 4000) as $m) {
        foreach (range(1, 4000) as $a) {
            foreach (range(1, 4000) as $s) {
                //if (Workflow::get('in')->accepts(['x'=>$m,'m'=>$m,'a'=>$a,'s'=>$s])) $p2++;
            }
        }
    }
}

echo "p2: {$p2}\n";
