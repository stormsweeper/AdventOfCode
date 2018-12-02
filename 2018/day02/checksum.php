<?php

$boxids = array_map(
    function($line) {
        return count_chars(trim($line), 1);
    },
    file($argv[1])
);

$with2 = array_filter(
    $boxids,
    function($id) {
        return array_search(2, $id, true);
    }
);

$with3 = array_filter(
    $boxids,
    function($id) {
        return array_search(3, $id, true);
    }
);


echo count($with2) * count($with3);