<?php

$input = trim(file_get_contents($argv[1]));
list($deck1, $deck2) = explode("\n\n", $input);
$deck1 = array_map('intval', explode("\n", substr($deck1, 10)));
$deck2 = array_map('intval', explode("\n", substr($deck2, 10)));

while ($deck1 && $deck2) {
    $card1 = array_shift($deck1);
    $card2 = array_shift($deck2);
    if ($card1 > $card2) {
        $deck1[] = $card1;
        $deck1[] = $card2;
    } else {
        $deck2[] = $card2;
        $deck2[] = $card1;        
    }
}

if (!$deck2) {
    $winner = $deck1;
} else {
    $winner = $deck2;
}

$score = 0;
$size = count($winner);
foreach ($winner as $i => $card) {
    $score += $card * ($size - $i);
}

echo $score;