<?php

function parsedata(string $txt): array {
    $data = [];
    $txt = str_replace("\n", ', ', $txt);
    foreach (explode(', ', $txt) as $stat) {
        list($k, $v) = explode(': ', $stat);
        $data[$k] = $v;
    }
    return $data;
}

$MFCSAM_output = parsedata(trim(file_get_contents($argv[1])));

$aunts = trim(file_get_contents($argv[2]));
$aunts = explode("\n", $aunts);

$possible_aunts = [];

$part2_aunts = [];

foreach ($aunts as $i => $line) {
    list(, $aunt_txt) = explode(': ', $line, 2);
    $aunt = parsedata($aunt_txt);
    // part 1
    $match = array_intersect_assoc($MFCSAM_output, $aunt);
    if (!empty($match)) {
        $possible_aunts[ $i + 1 ] = $match;
    }
    // part 2
    if (isset($aunt['cats']) && $aunt['cats'] <= $MFCSAM_output['cats']) continue;
    if (isset($aunt['trees']) && $aunt['trees'] <= $MFCSAM_output['trees']) continue;
    if (isset($aunt['pomeranians']) && $aunt['pomeranians'] >= $MFCSAM_output['pomeranians']) continue;
    if (isset($aunt['goldfish']) && $aunt['goldfish'] >= $MFCSAM_output['goldfish']) continue;
    if (isset($aunt['children']) && $aunt['children'] !== $MFCSAM_output['children']) continue;
    if (isset($aunt['samoyeds']) && $aunt['samoyeds'] !== $MFCSAM_output['samoyeds']) continue;
    if (isset($aunt['akitas']) && $aunt['akitas'] !== $MFCSAM_output['akitas']) continue;
    if (isset($aunt['vizslas']) && $aunt['vizslas'] !== $MFCSAM_output['vizslas']) continue;
    if (isset($aunt['cars']) && $aunt['cars'] !== $MFCSAM_output['cars']) continue;
    if (isset($aunt['perfumes']) && $aunt['perfumes'] !== $MFCSAM_output['perfumes']) continue;
    $part2_aunts[ $i + 1 ] = $aunt;        
}

echo json_encode($possible_aunts, JSON_PRETTY_PRINT);

// part 2;
echo json_encode($part2_aunts, JSON_PRETTY_PRINT);