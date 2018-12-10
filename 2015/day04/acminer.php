<?php

$secret = trim($argv[1]);
$prefix = str_repeat('0', intval($argv[2] ?? 5));

for ($num = 1; $num < PHP_INT_MAX; $num++) {
    $hash = md5($secret . $num);
    if (strpos($hash, $prefix) === 0) {
        break;
    }
}

echo $num;