<?php

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


function collapseEmptyPaths($path) {

    if (strpos($path, '|)') === false) {
        return $path;
    }

    $empty_path = '/\([NEWS\|]+\|\)/';
    return preg_replace($empty_path, '', $path);
}

function chooseLongestBranches($path) {
    $branch = '/\([NEWS\|]+[NEWS]\)/';
    $path = preg_replace_callback(
        $branch,
        function($matches) {
            $longest = '';
            $branches = explode('|', substr($matches[0], 1, -1));
            foreach ($branches as $branch) {
                if (strlen($branch) > strlen($longest)) {
                    $longest = $branch;
                }
            }
            return $longest;
        },
        $path
    );
    return $path;
}

$max = 100000;
do {

    // collapse any empty paths (i.e. ends in ())
    $input_path = collapseEmptyPaths($input_path);
    // longest path of (NEWS|NEWS)
    $input_path = chooseLongestBranches($input_path);

} while (strpos($input_path, '(') !== false && --$max);

echo strlen($input_path);