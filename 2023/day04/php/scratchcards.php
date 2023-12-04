<?php

$cards = fopen($argv[1], 'r');


function count_matching(string $card): int {
    [, $numbers] = explode(': ', $card, 2);
    [$winning, $mine] = explode(' | ', $numbers, 2);
    $winning = array_filter(explode(' ', $winning));
    $mine = array_filter(explode(' ', $mine));
    $matching = array_intersect($mine, $winning);
    return count($matching);
}

$total = 0;
$scored = [];
while (($card = fgets($cards)) !== false) {
    $scored[] = $matching = count_matching(trim($card));
    if ($matching) $total += pow(2, $matching - 1);
}

echo "part 1: {$total}\n";

$unique_cards = array_keys($scored);
function count_all_cards(array $cards, int $depth = 0): int {
    global $unique_cards, $scored;
    $count = count($cards);
    foreach ($cards as $c) {
        if ($scored[$c]) {
            $extra = array_slice($unique_cards, $c + 1, $scored[$c]);
            $count += count_all_cards($extra, $depth + 1);
        }
    } 
    return $count;
}

$total_cards = count_all_cards($unique_cards);

echo "part 2: {$total_cards}\n";
