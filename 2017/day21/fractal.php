<?php


function chunkImage(string $img): array {
    $size = sqrt(strlen($img));
    // being lazy as there won't be multiples of other primes besides 2/3,
    // and even multiples of 3 should use 2
    $divisor = $size & 1 ? 3 : 2;
    $count = $size / $divisor;
    $chunks = [];

    for ($vchunk = 0; $vchunk < $size; $vchunk += $divisor) {
        for ($hchunk = 0; $hchunk < $size; $hchunk += $divisor) {
            $chunk = '';
            for ($row = 0; $row < $divisor; $row++) {
                $offset = $vchunk * $size + $row * $size;
                $chunk .= substr($img, $offset + $hchunk, $divisor);
            }
            $chunks[] = $chunk;
        }
    }

    return $chunks;
}

function combine(array $chunks): string {
    $size = sqrt(count($chunks));
    $width = sqrt(strlen($chunks[0]));
    $combined = '';
    for ($slice = 0; $slice < $size; $slice++) {
        for ($row = 0; $row < $width; $row++) {
            for ($chunk = 0; $chunk < $size; $chunk++) {
                $i = $slice * $size + $chunk;
                $combined .= substr($chunks[$i], $row * $width, $width);
            }
        }
    }
    return $combined;
}

function expand(string $chunk): string {
    global $transforms;
    if (!empty($transforms[$chunk])) {
        return $transforms[$chunk];
    }
    $alts = alternates($chunk);
    //print_r($alts); exit();
    foreach ($alts as $alt) {
        if (!empty($transforms[$alt])) {
            return $transforms[$alt];
        }
    }
    return strlen($chunk) === 4 ? str_repeat('?', 9) : str_repeat('?', 16) ;
}

function alternates(string $chunk) {
    $alternates = [];
    $size = sqrt(strlen($chunk));
    $lines = str_split($chunk, $size);

    // h flip
    $alternates['hflip'] = implode(array_map('strrev', $lines));

    // v flip
    $alternates['vflip'] = implode(array_reverse($lines));

    // h+v flip
    $alternates['hvflip'] = implode(array_map('strrev', array_reverse($lines)));

    // tilts
    $tilted = array_pad([], $size, '');
    for ($x = 0; $x < $size; $x++) {
        for ($y = 0; $y < $size; $y++) {
            $tilted[$y] .= $lines[$x][$y];
        }
    }

    // tilt
    $alternates['tilt'] = implode($tilted);

    // h flip
    $alternates['tilt-hflip'] = implode(array_map('strrev', $tilted));

    // v flip
    $alternates['tilt-vflip'] = implode(array_reverse($tilted));

    // h+v flip
    $alternates['tilt-hvflip'] = implode(array_map('strrev', array_reverse($tilted)));

    return $alternates;
}

function pixelCount(string $img) {
    str_replace('#', '', $img, $count);
    return $count;
}

function pretty(string $img) {
    $len = sqrt(strlen($img));
    $img = str_split($img, $len);
    return implode("\n", $img);
}

$img = '.#...####';

$test = '#######.#.#.#######.#.#.#######.#.#.';

$transforms = [];
$input = explode("\n", file_get_contents($argv[1]));
foreach ($input as $line) {
    list ($from, $to) = str_replace('/', '', explode(' => ', $line));
    $transforms[ $from] = $to;
}

$iterations = intval($argv[2] ?? 1);
while ($iterations--) {
    $chunked = array_map('expand', chunkImage($img));
    $img = combine($chunked);
}

echo pretty($img);

$pxc = pixelCount($img);
echo "\ncount: {$pxc}\n";