<?php

$input = fopen($argv[1], 'r');

$left = $right = [];

$num = 0;
while (($line = fgets($input)) !== false) {
    [$left[], $right[]] = array_map('intval', explode('   ', $line, 2));
    $num++;
}

sort($left);
sort($right);

$dist = 0;

for ($i = 0; $i < $num; $i++) {
    $dist += abs($left[$i] - $right[$i]);
}

echo "p1: {$dist}\n";

$right_counts = array_count_values($right);

$score = 0;
for ($i = 0; $i < $num; $i++) {
    $score += $left[$i] * ($right_counts[$left[$i]] ?? 0);
}

echo "p1: {$score}\n";
