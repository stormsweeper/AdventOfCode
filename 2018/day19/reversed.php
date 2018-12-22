<?php

$sum = 0;

$target = 10551428;
for ($i = 1; $i <= $target; $i++) {
    if ($target % $i === 0) {
        $sum += $i;
    }
}

echo $sum;
