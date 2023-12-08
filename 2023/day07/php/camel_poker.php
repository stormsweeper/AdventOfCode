
<?php

$hands = trim(file_get_contents($argv[1]));

function parse_hand(string $hand): array {
    [$hand, $bid] = explode(' ', $hand);
    $bid = intval($bid);
    $wild_hand = wild_hand($hand);
    return [
        $hand,
        $bid,
        rank_hand($hand),
        sort_val($hand),
        rank_hand($wild_hand),
        sort_val($hand, true),
    ];
}

define('HAND_TYPES', [
    'five of a kind'    => 6,
    'four of a kind'    => 5,
    'full house'        => 4,
    'three of a kind'   => 3,
    'two pair'          => 2,
    'one pair'          => 1,
    'high card'         => 0,
]);

function sort_val(string $hand, bool $wild = false): int {
    return hexdec(strtr($hand, [
        'T' => 'a', 
        'J' => $wild ? 1 : 'b',
        'Q' => 'c',
        'K' => 'd',
        'A' => 'e',
    ]));

}

function rank_hand(string $hand): int {
    $sorted = array_values(count_chars($hand, 1));
    rsort($sorted);
    if (count($sorted) === 1) return HAND_TYPES['five of a kind'];
    if (count($sorted) === 2 && $sorted[0] === 4) return HAND_TYPES['four of a kind'];
    if (count($sorted) === 2 && $sorted[0] === 3) return HAND_TYPES['full house'];
    if (count($sorted) === 3 && $sorted[0] === 3) return HAND_TYPES['three of a kind'];
    if (count($sorted) === 3 && $sorted[0] === 2) return HAND_TYPES['two pair'];
    if (count($sorted) === 4) return HAND_TYPES['one pair'];
    if (count($sorted) === 5) return HAND_TYPES['high card'];
    throw new  RuntimeException('could not rank hand: '.$hand);
}

function wild_hand(string $hand): string {
    if (strpos($hand, 'J') === false || $hand === 'JJJJJ') return $hand;

    $counts = count_chars($hand, 1);
    $num_jokers = $counts[ord('J')];
    unset($counts[ord('J')]);
    arsort($counts);
    $best_real = chr(array_keys($counts)[0]);
    return strtr($hand, 'J', $best_real);
}

function arrange_hand(string $hand): string { return $hand;
    $cards = str_split($hand);
    rsort($cards);
    return implode('', $cards);
}

$hands = array_map('parse_hand', explode("\n", $hands));

usort($hands, function ($a, $b) {
    $by_type = $a[2] <=> $b[2];
    if ($by_type === 0) {
        return $a[3] <=> $b[3];
    }
    return $by_type;
});

$score = 0;

foreach ($hands as $rank => [$hand, $bid]) {
    $score += $bid * ($rank + 1);
}

echo "p1: {$score}\n";

usort($hands, function ($a, $b) {
    $by_type = $a[4] <=> $b[4];
    if ($by_type === 0) {
        return $a[5] <=> $b[5];
    }
    return $by_type;
});

$score = 0;

foreach ($hands as $rank => [$hand, $bid, , $sort_val, , $wild_hand ]) {
    $score += $bid * ($rank + 1);
}

echo "p2: {$score}\n";
