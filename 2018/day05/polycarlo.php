<?php

$data = trim(file_get_contents($argv[1]));
//$data = 'dabAcCaCBAcCcaDA';

function countReduced($data, $delete) {
    $data = str_ireplace($delete, '', $data);
    $data = str_split($data);
    $final = array_reduce(
        $data,
        function($carry, $char) {
    
            $last_char = $carry[-1] ?? '';
            if ($last_char !== $char && strcasecmp($last_char, $char) === 0) {
                return substr($carry, 0, -1);
            }
    
            return $carry . $char;
        },
        ''
    );
    return strlen($final);
}

$min = PHP_INT_MAX;

// ASCII
for ($c = 65; $c <= 97; $c++) {
    $min = min($min, countReduced($data, chr($c)));
}

echo $min;
