<?php


$input = trim(file_get_contents($argv[1]));

function applyFFTPart1(string $data, int $max_phases = 100): string {
    $input_length = strlen($data);
    for ($phase = 0; $phase < $max_phases; $phase++) {
        //echo "Starting phase {$phase}\n";
        $next = '';
        for ($step = 1; $step <= $input_length; $step++) {
            $val = 0;
            for ($i = ($step - 1); $i < $input_length; $i += ($step * 4)) {
                // add this swatch
                $add_start = $i;
                for ($j = $add_start; $j < ($add_start + $step) && $j < $input_length; $j++) {
                    $val += $data[$j];
                }
                $sub = substr($data, $i + ($step * 2), $step);
                // sub this swatch
                $sub_start = $i + ($step * 2);
                for ($j = $sub_start; $j < ($sub_start + $step) && $j < $input_length; $j++) {
                    $val -= $data[$j];
                }
            }
            $next .= (abs($val) % 10);
        }
        //echo "after transform: {$next}\n";
        $data = $next;
    }
    return $data;
}

function applyFFTPart2(string $data, int $max_phases = 100): string {
    $input_length = strlen($data);
    for ($phase = 0; $phase < $max_phases; $phase++) {
        //echo "Starting phase {$phase}\n";
        $next = $data;
        $last_sum = 0;
        for ($i = $input_length - 1; $i >= 0; $i--) {
            $next[$i] = $last_sum = ($last_sum + $data[$i]) % 10;
        }
        //echo "after transform: {$next}\n";
        $data = $next;
    }
    return $data;
}

$phases = 100;

echo "PART 1:\n";

$s = hrtime(true);
$result = applyFFTPart1($input, $phases);
$result = substr($result, 0, 8);
$e = hrtime(true) - $s;
echo "after {$phases} phases: {$result} ({$e}ns)\n";


echo "PART 2:\n";

$offset = intval(substr($input, 0, 7));
$input_len = strlen($input);
$total_len = $input_len * 10000;
$needed = $total_len - $offset;
$multi = ceil($needed / $input_len);
$repeated = str_repeat($input, $multi);
$repeated = substr($repeated, 0 - $needed);

$s = hrtime(true);
$result = applyFFTPart2($repeated, $phases);
$result = substr($result, 0, 8);
$e = hrtime(true) - $s;
echo "after {$phases} phases: {$result} ({$e}ns)\n";


