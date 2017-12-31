<?php

require_once __DIR__ . '/knothashclass.php';

$input = $argv[1];
$drive = [];
$mapped = [];
$regions = 0;

// fill the drive
for ($rownum = 0; $rownum < 128; $rownum++) {
    $kh = new KnotHash("{$input}-{$rownum}");
    $drive[$rownum] = str_split($kh->toBin());
}

function mapRegion($rownum, $colnum) {
    global $drive, $mapped, $regions;
    // if already mapped, return
    if (isset($mapped[$rownum][$colnum])) {
        return;
    }

    // set current pos
    $mapped[$rownum][$colnum] = $regions;

    // find adjacent set in drive
    if (!empty($drive[$rownum + 1][$colnum])) {
        mapRegion($rownum + 1, $colnum);
    }
    if (!empty($drive[$rownum - 1][$colnum])) {
        mapRegion($rownum - 1, $colnum);
    }
    if (!empty($drive[$rownum][$colnum + 1])) {
        mapRegion($rownum, $colnum + 1);
    }
    if (!empty($drive[$rownum][$colnum - 1])) {
        mapRegion($rownum, $colnum - 1);
    }
}

for ($rownum = 0; $rownum < 128; $rownum++) {
    for ($colnum = 0; $colnum < 128; $colnum++) {
        // if mapped, continue
        if (isset($mapped[$rownum][$colnum])) {
            continue;
        }

        // if not used in drive, continue
        if (empty($drive[$rownum][$colnum])) {
            continue;
        }

        // map new region
        $regions++;
        mapRegion($rownum, $colnum);
    }
}

echo $regions;