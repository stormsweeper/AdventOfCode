<?php

$inst = file_get_contents($argv[1]);
$counts = count_chars($inst);

print_r($counts, 1);

echo $counts[ord('(')] - $counts[ord(')')];