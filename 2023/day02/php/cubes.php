<?php

$games = fopen($argv[1], 'r');

function parse_game(string $game): array {
    $counts = ['red' => 0, 'green' => 0, 'blue' => 0];
    [$id, $game] = explode(': ', substr($game, 5));
    foreach (explode('; ', $game) as $round) {
        foreach (explode(', ', $round) as $set) {
            [$num, $color] = explode(' ', $set);
            $counts[$color] = max(intval($num), $counts[$color]);
        }
    }
    return [intval($id), $counts];
}

$compare = ['red' => 12, 'green' => 13, 'blue' => 14];
function possible($counts): bool {
    global $compare;
    foreach ($counts as $color => $num) {
        if ($num > $compare[$color]) return false;
    }
    return true;
}

$total = $power = 0;
while (($game = fgets($games)) !== false) {
    $game = trim($game);
    if (!$game) continue;

    [$id, $counts] = parse_game($game);
    if (possible($counts)) $total += $id;
    $power += array_product($counts);
}

echo $total . "\n";
echo $power . "\n";
