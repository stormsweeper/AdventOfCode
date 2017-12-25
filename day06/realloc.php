<?php

$membanks = [14,0,15,12,11,11,3,5,1,6,8,4,9,1,8,4];
$numbanks = count($membanks);
$seen = [];

function getLargestPos($membanks) {
    $max = 0;
    $lpos = 0;
    foreach ($membanks as $pos => $bank) {
        if ($bank > $max) {
            $max = $bank;
            $lpos = $pos;
        }
    }
    return $lpos;
}

while (!in_array($membanks, $seen)) {
    $seen[] = $membanks;
    $drainpos = getLargestPos($membanks);
    $redist = $membanks[$drainpos];
    $membanks[$drainpos] = 0;
    while ($redist-- > 0) {
        $drainpos = ($drainpos + 1) % $numbanks;
        $membanks[$drainpos]++;
    }
}
$count = count($seen);
$hmm = array_search($membanks, $seen);
$diff = $count - $hmm;
echo "loops {$count} / {$hmm} / {$diff}";

