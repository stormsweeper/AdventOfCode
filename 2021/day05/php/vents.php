<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

// sparse array of ["x,y" => n]
$grid = [];


function addVent(&$grid, $x1, $y1, $x2, $y2) {
    if ($x1 !== $x2) {
        foreach (range($x1, $x2) as $x) {
            $k = "{$x},{$y1}";
            $grid[$k] = ($grid[$k]??0) + 1;
        }    
    }
    elseif ($y1 !== $y2) {
        foreach (range($y1, $y2) as $y) {
            $k = "{$x1},{$y}";
            $grid[$k] = ($grid[$k]??0) + 1;
        }
    }
}

foreach ($inputs as $line) {
    list($x1, $y1, $x2, $y2) = preg_split('/\D+/', $line);
    if ( $x1===$x2 || $y1===$y2 ) {
        addVent($grid, $x1, $y1, $x2, $y2);
    }
}

$danger = array_filter($grid, function($v) {return $v > 1;});
echo count($danger);