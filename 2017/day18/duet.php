<?php

$assembly = explode("\n", file_get_contents($argv[1]));

$registers = [];
$inst = 0;

function read($reg) {
    global $registers;
    if (is_numeric($reg)) {
        return $reg;
    }
    return $registers[$reg] ?? 0;
}

function snd($freq) {
    set('freq', read($freq));
}

function set($reg, $val) {
    global $registers;
    return $registers[$reg] = read($val);
}

function add($reg, $add) {
    set($reg, read($reg) + read($add));
}

function mul($reg, $mul) {
    set($reg, read($reg) * read($mul));
}

function mod($reg, $mod) {
    set($reg, read($reg) % read($mod));
}

function rcv($reg) {
    if (read($reg)) {
        exit(strval(read('freq')));
    }
}

function jgz($reg, $jump) {
    global $inst;
    if (read($reg)) {
        $inst += read($jump) - 1;
    }
}

while ($inst < count($assembly)) {
    $args = explode(' ', $assembly[$inst++]);
    $cmd = array_shift($args);
    call_user_func_array($cmd, $args);
}