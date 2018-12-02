<?php

function withPort($port, $pipes) {
    return array_filter(
        $pipes,
        function($pipe) use ($port){
            return $pipe[0] === $port || $pipe[1] === $port;
        }
    );
}

function rightPort($left, $pipe) {
    return $pipe[0] === $left ? $pipe[1] : $pipe[0];
}

function buildBridges($port, $pipes, $seen = []) {
    $next = withPort($port, $pipes);

    foreach ($next as $pid => $pipe) {
        $sofar = $seen;
        $sofar[$pid] = $pipe;
        $remainder = $pipes;
        //echo 'sofar ' . printBridge($sofar) . "\n";
        unset($remainder[$pid]);
        yield $sofar;
        yield from buildBridges(rightPort($port, $pipe), $remainder, $sofar);
    }
}

function printBridge($bridge) {
    $bridge = array_map(
        function($pipe) {
            return implode('/', $pipe);
        },
        $bridge
    );
    return implode('--', $bridge);
}

function weight($bridge) {
    return array_sum(array_map('array_sum', $bridge));
}

$pipes = explode("\n", file_get_contents($argv[1]));
$pipes = array_map(
    function($pipe) {
        return array_map('intval', explode('/', $pipe));
    },
    $pipes
);

$maxweight = 0;
$longest = 0;
$maxlongest = 0;

foreach (buildBridges(0, $pipes) as $bridge) {
    $length = count($bridge);
    $weight = weight($bridge);

    $maxweight = max($maxweight, $weight);
    if ($length >= $longest) {
        $longest = $length;
        $maxlongest = max($maxlongest, $weight);
    }
}

print_r([$maxweight, $longest, $maxlongest]);

