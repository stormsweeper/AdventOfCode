<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

define('VALID_CHUNKS', ['[]','{}','()','<>']);
define('INVALID_SCORES', [')'=>3, ']'=>57, '}'=>1197, '>'=>25137]);
define('INC_SCORES', ['('=>1, '['=>2, '{'=>3, '<'=>4]);

function score_invalid(string $line): int {
    $replaced = 0;
    do {
        $line = str_replace(VALID_CHUNKS, '', $line, $replaced);
    }
    while ($replaced > 0);
    if ($line === '') return 0;
    if (preg_match('/[\[(<{][\])>}]/', $line, $m)) {
        return INVALID_SCORES[$m[0][1]];
    }
    return 0;
}

$invalid_score = 0;
$inc_scores = [];
foreach ($inputs as $line) {
    // strip out the good chunks
    $replaced = 0;
    do {
        $line = str_replace(VALID_CHUNKS, '', $line, $replaced);
    }
    while ($replaced > 0);
    // find invalid
    if (preg_match('/[\[(<{][\])>}]/', $line, $m)) {
        $invalid_score += INVALID_SCORES[$m[0][1]];
        continue;
    }
    // line now should only be incomplete part
    $score = 0;
    for ($i = strlen($line) - 1; $i >= 0; $i--) {
        $score = $score * 5 + INC_SCORES[$line[$i]];
    }
    $inc_scores[] = $score;

}

sort($inc_scores);
$med = $inc_scores[ floor(count($inc_scores)/2) ];

echo "p1:{$invalid_score} p2:{$med}\n";

