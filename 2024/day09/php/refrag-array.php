<?php

$diskmap = trim(file_get_contents($argv[1]));


$disk = [];

$len = strlen($diskmap);

function checksum(array $disk): int {
    $checksum = 0;
    foreach ($disk as $pos => $block) $checksum += $pos * $block;
    return $checksum;
}

$pos = 0;
$lscan = null;
for ($i = 0; $i < $len; $i++) {
    $size = intval($diskmap[$i]);
    if ($i%2 === 0) {
        $id = $i/2;
        $files[$id] = [$pos, $size];
        for ($o = 0; $o < $size; $o++) $disk[$pos + $o] = $id;
    } elseif (!isset($lscan)) {
        $lscan = $pos;
    }
    $pos += $size;
}

$rscan = $pos - 1;

$unfragged = $disk;

while ($lscan < $rscan) {
    if (!isset($unfragged[$lscan])) {
        while ($rscan > $lscan) {
            if (isset($unfragged[$rscan])) {
                $unfragged[$lscan] = $unfragged[$rscan];
                unset($unfragged[$rscan]);
                break;
            }
            $rscan--;
        }
    }
    $lscan++;
}

$p1 = checksum($unfragged);
echo "p1: {$p1}\n";

function find_free(array $disk, int $before, int $size): int {
    for ($pos = 0; $pos < $before; $pos++) {
        for ($o = 0; $o < $size; $o++) {
            if (isset($disk[$pos + $o])) continue 2;
        }
        return $pos;
    }
    return -1;
}

$uncompacted = $disk;

$fid = count($files) - 1;

$after = $files[0][1] - 1;

while ($fid > 0) {
    [$before, $size] = $files[$fid];
    // get first
    $free = find_free($uncompacted, $before, $size);
    // move file if we can
    if ($free !== -1) {
        for ($o = 0; $o < $size; $o++) {
            $uncompacted[$free + $o] = $uncompacted[$before + $o];
            unset($uncompacted[$before + $o]);
        }
    }
    $fid--;
}

// ksort($uncompacted);
// print_r($uncompacted);

$p2 = checksum($uncompacted);
echo "p2: {$p2}\n";
