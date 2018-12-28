<?php

function nextFrom($pos, string $dir) {
    switch ($dir) {
        case 'N': $pos[1]--; break;
        case 'S': $pos[1]++; break;
        case 'E': $pos[0]--; break;
        case 'W': $pos[0]++; break;
    }

    return $pos;
}

// example 1, result should be 3
//$input = '^WNE$';
// example 2, result should be 10
//$input = '^ENWWW(NEEE|SSE(EE|N))$';
// example 3, result should be 18
//$input = '^ENNWSWW(NEWS|)SSSEEN(WNSE|)EE(SWEN|)NNN$';
// example 4, 23 doors
//$input = '^ESSWWN(E|NNENN(EESS(WNSE|)SSS|WWWSSSSE(SW|NNNE)))$';
// example 5, 31
//$input = '^WSSEESWWWNW(S|NENNEEEENN(ESSSSW(NWSW|SSEN)|WSWWN(E|WWS(E|SS))))$';

$input = trim(file_get_contents($argv[1]));

// we don't actually need these 
$input_path = trim($input, '^$');

$mapped = [
    '0,0' => 0,
];

$current = [0,0];
$dist = 0;
$branches = [];

$max_steps = strlen($input_path);

for ($i = 0; $i < $max_steps; $i ++) {
    $step = $input_path[$i];

    switch ($step) {
        case 'N':
        case 'E':
        case 'S':
        case 'W':
            // take step
            $dist++;
            $current = nextFrom($current, $step);
            $key = "{$current[0]},{$current[1]}";
            if (isset($mapped[$key])) {
                if ($mapped[$key] > $dist) {
                    $mapped[$key] = $dist;
                }
            }
            else {
                $mapped[$key] = $dist;
            }
            break;

        case '(':
            // start branch
            $branches[] = [$current, $dist];
            break;

        case '|':
            // backtrack to start of branch branch
            [$current, $dist] = $branches[ count($branches) - 1 ];
            break;

        case ')':
            // end branch
            [$current, $dist] = array_pop($branches);
            break;
    }
}

$furthest = array_reduce(
    $mapped,
    function($carry, $item) {
        if (isset($carry) && $carry > $item) {
            return $carry;
        }
        return $item;
    }
);

echo "Furthest room: {$furthest}\n";

$distant_rooms = array_filter(
    $mapped,
    function($dist) {
        return $dist >= 1000;
    }
);
$num_rooms = count($distant_rooms);
echo "Distant rooms: {$num_rooms}\n";