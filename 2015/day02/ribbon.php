<?php

$ribbons = array_map(
    function($dim) {
        if (strpos($dim, 'x') === false) {
            return 0;
        }
        $dim = array_map('intval', explode('x', $dim));
        sort($dim);
        return (2 * ($dim[0] + $dim[1])) + array_product($dim);
    },
    file($argv[1])
);


echo array_sum($ribbons);