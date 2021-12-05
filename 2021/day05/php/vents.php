<?php

$inputs = trim(file_get_contents($argv[1]));
$inputs = explode("\n", $inputs);

// sparse array of ["x,y" => n]
$hv_grid = $cart_grid = [];


function addVent(&$grid, $x1, $y1, $x2, $y2) {
    $xrange = range($x1, $x2);
    $yrange = range($y1, $y2);
    $dist = max(count($xrange), count($yrange));
    if (count($xrange) < $dist) {
        $xrange = array_fill(0, $dist, $xrange[0]);
    }
    if (count($yrange) < $dist) {
        $yrange = array_fill(0, $dist, $yrange[0]);
    }
    for ($i = 0; $i < $dist; $i++) {
        $k = "{$xrange[$i]},{$yrange[$i]}";
        $grid[$k] = ($grid[$k]??0) + 1;
    }
}

foreach ($inputs as $line) {
    list($x1, $y1, $x2, $y2) = preg_split('/\D+/', $line);
    if ( $x1===$x2 || $y1===$y2 ) {
        addVent($hv_grid, $x1, $y1, $x2, $y2);
    }
}

$hv_danger = array_filter($hv_grid, function($v) {return $v > 1;});
$hv_danger = count($hv_danger);

echo $hv_danger;
