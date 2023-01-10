<?php

#  ¯\_(ツ)_/¯
ini_set('memory_limit', '2G');

$data = trim(file_get_contents($argv[1]));

preg_match_all('/Valve ([A-Z][A-Z]) has flow rate=(\d+); tunnels? leads? to valves? ([A-Z, ]+)/', $data, $matches, PREG_SET_ORDER);

$valve_names = $flow_rates = $distances = $zero_valves = [];
$total_valves = 0;

foreach ($matches as [, $valve, $flow, $connections]) {
    $total_valves++;
    $valve_names[$valve] = $valve;
    $flow_rates[$valve] = $flow = intval($flow);
    if ($flow === 0) $zero_valves[$valve] = true;
    if (!isset($distances[$valve])) $distances[$valve] = [];
    $connections = explode(', ', $connections);
    foreach ($connections as $conn) {
        if (!isset($distances[$conn])) $distances[$conn] = [];
        $distances[$valve][$conn] = $distances[$conn][$valve] = 1;
    }
}

// map out distances from every valve to each other
do {
    $updated = false;
    $firsts = array_keys($distances);
    foreach ($firsts as $first) {
        $seconds = $distances[$first];
        foreach ($seconds as $second => $second_dist) {
            $thirds = $distances[$second];
            foreach ($thirds as $third => $dist) {
                if ($first === $third) continue;
                $ndist = $second_dist + $dist;
                if (!isset($distances[$first][$third]) || $distances[$first][$third] > $ndist) {
                    $distances[$first][$third] = $distances[$third][$first] = $ndist;
                    $updated = true;
                }
            }
        }
    }
} while ($updated);

// print_r($distances); exit;

function max_flow_from(string $current_pos, int $remaining_time, array $open_valves): int {
    global $flow_rates, $distances;
    $current_flow = 0;
    if (!isset($open_valves[$current_pos])) {
        $open_valves[$current_pos] = true;
        $remaining_time--;
        $current_flow = $flow_rates[$current_pos] * $remaining_time;
    }
    $still_closed = array_diff_key($distances[$current_pos], $open_valves);
    $max_extra = 0;
    foreach ($still_closed as $valve => $dist) {
        if ($remaining_time - $dist > 0) {
            $extra = max_flow_from($valve, $remaining_time - $dist, $open_valves);
            $max_extra = max($max_extra, $extra);
        }
    }
    return $current_flow + $max_extra;
}


$p1 = max_flow_from('AA', 30, $zero_valves);

function max_partner_flow(string $my_pos, int $my_time, string $el_pos, int $el_time, array $open_valves): int {
    global $flow_rates, $distances, $valve_names;
    // echo "called: {$current_pos} {$remaining_time} \n";
    $current_flow = $max_extra = 0;
    $open_valves[$my_pos] = true;
    $open_valves[$el_pos] = true;
    if ($my_pos !== 'AA' && $my_time > 0) {
        $my_time--;
        $current_flow += $flow_rates[$my_pos] * $my_time;
    }
    if ($el_pos !== 'AA' && $el_time > 0) {
        $el_time--;
        $current_flow += $flow_rates[$el_pos] * $el_time;
    }

    // need to open all, so shortcut if out of time
    if ($my_time < 2 && $el_time < 2) return $current_flow;

    $still_closed = array_diff_key($valve_names, $open_valves);
    if (count($still_closed) < 2) {
        $max_extra = max(
            max_flow_from($el_pos, $el_time, $open_valves),
            max_flow_from($my_pos, $my_time, $open_valves),
        );
    }
    elseif ($el_time < 2) {
        $max_extra = max_flow_from($my_pos, $my_time, $open_valves);
    }
    elseif ($my_time < 2) {
        $max_extra = max_flow_from($el_pos, $el_time, $open_valves);
    }
    else {
        foreach ($still_closed as $my_valve) {
            foreach ($still_closed as $el_valve) {
                if ($my_valve === $el_valve) continue;
                $extra = max_partner_flow(
                    $my_valve,
                    $my_time > 1 ? ($my_time - $distances[$my_pos][$my_valve]) : 0,
                    $el_valve,
                    $el_time > 1 ? ($el_time - $distances[$el_pos][$el_valve]) : 0,
                    $open_valves
                );
                $max_extra = max($max_extra, $extra);
            }
        }
    }
    return $current_flow + $max_extra;
}

$p2 = max_partner_flow('AA', 26, 'AA', 26, $zero_valves);

echo "p1: {$p1} p2: {$p2}\n";
