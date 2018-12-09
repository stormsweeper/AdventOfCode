<?php

$gifts = array_map(
    function($dim) {
        if (strpos($dim, 'x') === false) {
            return 0;
        }
        [$l, $w, $h] = array_map('intval', explode('x', $dim));
        $bits = [
            2 * $l * $w,
            2 * $l * $h,
            2 * $w * $h,
        ];
        $bits[] = min($bits) / 2;
        return array_sum($bits);
    },
    file($argv[1])
);


echo array_sum($gifts);