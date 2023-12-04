<?php

$cards = fopen($argv[1], 'r');


function score_card(string $card): int {
    [, $numbers] = explode(': ', $card, 2);
    [$winning, $mine] = explode(' | ', $numbers, 2);
    $winning = array_filter(explode(' ', $winning));
    $mine = array_filter(explode(' ', $mine));
    $matching = array_intersect($mine, $winning);
    if (!$matching) return 0;
    return pow(2, count($matching) - 1);
}

$total = 0;
while (($card = fgets($cards)) !== false) {
    $total += score_card(trim($card));
}

echo $total . "\n";