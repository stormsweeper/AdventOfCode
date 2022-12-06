<?php

$signal = trim(file_get_contents('php://stdin'));

$idx = 0;
$message_header = null;

while (!isset($packet_header) && $idx < strlen($signal)) {
    $check = substr($signal, $idx, 4);
    if (strlen(count_chars($check, 3)) === 4) {
        $packet_header = $check;
        break;
    }
    $idx++;
}

$p1 = $idx + 4;

$idx = 0;
$message_header = null;
while (!isset($message_header) && $idx < strlen($signal)) {
    $check = substr($signal, $idx, 14);
    if (strlen(count_chars($check, 3)) === 14) {
        $message_header = $check;
        break;
    }
    $idx++;
}

$p2 = $idx + 14;

echo "p1: {$p1} p2: {$p2}\n";