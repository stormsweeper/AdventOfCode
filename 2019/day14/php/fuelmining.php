<?php

$input = trim(file_get_contents($argv[1]));
$input = explode("\n", $input);


function parseComponent(string $part): array {
    list ($qty, $type) = explode(' ', $part);
    return [intval($qty), $type];
}

function parseReaction(string $equation): array {
    list ($inputs, $output) = explode(' => ', $equation);
    list ($oqty, $otype) = parseComponent($output);
    $reqs = [];
    foreach (explode(', ', $inputs) as $part)  {
        list ($rqty, $rtype) = parseComponent($part);
        $reqs[$rtype] = $rqty;
    }
    return [
        $oqty,
        $otype,
        $reqs,
    ];
}

$reactions = [];
foreach ($input as $line) {
    list ($oqty, $otype, $reqs) = parseReaction($line);
    $reactions[$otype] = ['yield' => $oqty, 'inputs' => $reqs];    
}



function processReagents(string $type, int $qty, array &$excess, int &$total_ore, SplQueue &$queue) {
    global $reactions;
    //echo "processing: {$type} {$qty}\n";
    $r = $reactions[$type];

    // check if excess covers it already
    if (isset($excess[$type])) {
        if ($excess[$type] >= $qty) {
            $excess[$type] -= $qty;
            return;
        }
        $qty -= $excess[$type];
        $excess[$type] = 0;
    }

    // calc reactions needed, note excess
    $multiplier = ceil($qty / $r['yield']);
    $total_yield = $multiplier * $r['yield'];
    $excess[$type] = ($excess[$type] ?? 0) + ($total_yield - $qty);
    foreach ($r['inputs'] as $rtype => $rqty) {
        if ($rtype === 'ORE') {
            $total_ore += $rqty * $multiplier;
        } else {
            $queue->enqueue([$rtype, $rqty * $multiplier]);
        }
    }
}

function calculateRequiredOre(string $type, int $qty): int {
    $total_ore = 0;
    $excess = [];
    $queue = new SplQueue;
    $queue->enqueue([$type, $qty]);
    while (!$queue->isEmpty()) {
        list ($type, $qty) = $queue->dequeue();
        processReagents($type, $qty, $excess, $total_ore, $queue);
    }
    return $total_ore;
}

$ore_per_fuel = calculateRequiredOre('FUEL', 1);
echo $ore_per_fuel . "\n";

$max_ore = 1000000000000;
$max_fuel = floor($max_ore / $ore_per_fuel);

$total_req_ore = 0;
while ($total_req_ore < $max_ore) {
    $total_req_ore = calculateRequiredOre('FUEL', $max_fuel);
    //echo "fuel: {$max_fuel} from ore: {$total_req_ore}\n";
    $max_fuel++;
}

echo $max_fuel - 2;
