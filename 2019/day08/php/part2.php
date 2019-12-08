<?php

$input = trim(file_get_contents($argv[1]));
$width = intval($argv[2]);
$height = intval($argv[3]);

$dims = $width * $height;
$layers = str_split($input, $dims);
$output = str_repeat('2', $dims);



for ($i = 0; $i < $dims; $i++) {
    foreach ($layers as $li => $layer) {
        //echo "{$i} {$li} {$layer} \n";
        if ($output[$i] === '2' && $layer[$i] !== '2') {
            $output[$i] = $layer[$i];
            break;
        }
    }
}

$output = strtr($output, '0', ' ');
$output = str_split($output, $width);
echo implode("\n", $output) . "\n";