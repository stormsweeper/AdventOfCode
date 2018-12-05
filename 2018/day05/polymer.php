<?php

$data = trim(file_get_contents($argv[1]));
//$data = 'dabAcCaCBAcCcaDA';
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

echo strlen($final);

