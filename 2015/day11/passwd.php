<?php

$input = $argv[1];

$alpha_chars = range('a', 'z');
$b26_chars = array_merge(range(0, 9), range('a', 'p'));
$alpha_b26_map = array_combine($alpha_chars, $b26_chars);
$b26_alpha_map = array_combine($b26_chars, $alpha_chars);

function incpass(string $pass): string {
    $len = strlen($pass);
    $next = int2alpha(alpha2int($pass) + 1);
    return substr(str_pad($next, $len, 'a', STR_PAD_LEFT), 0 - $len);
}

function alpha2int(string $alpha): int {
    global $alpha_b26_map;
    return (int)base_convert(strtr($alpha, $alpha_b26_map), 26, 10);
}

function int2alpha(int $int): string {
    global $b26_alpha_map;
    return strtr(base_convert($int, 10, 26), $b26_alpha_map);
}

function does_not_contain(string $passwd, array $invalid): bool {
    foreach ($invalid as $i) {
        if (strpos($passwd, $i) !== false) {
            return false;
        }
    }
    return true;
}
assert(!does_not_contain('hijklmmn', ['i', 'o', 'l']));
assert(does_not_contain('abbceffg', ['i', 'o', 'l']));
assert(does_not_contain('abbcegjk', ['i', 'o', 'l']));

function has_straight(string $passwd): bool {
    global $alpha_chars;
    for ($i = 0; $i < 24; $i++) {
        $needle = $alpha_chars[$i] . $alpha_chars[$i + 1] . $alpha_chars[$i + 2];
        if (strpos($passwd, $needle) !== false) {
            return true;
        }
    }
    return false;
}
assert(has_straight('hijklmmn'));
assert(!has_straight('abbceffg'));
assert(!has_straight('abbcegjk'));

function has_pairs(string $passwd, int $req): bool {
    global $alpha_chars;
    $found = 0;
    foreach ($alpha_chars as $a) {
        if (strpos($passwd, "{$a}{$a}") !== false && ++$found >= $req) {
            break;
        }
    }
    return $found >= $req;
}
assert(!has_pairs('hijklmmn', 2));
assert(has_pairs('abbceffg', 2));
assert(!has_pairs('abbcegjk', 2));

function valid_passwd(string $passwd): bool {
    return has_straight($passwd) && does_not_contain($passwd, ['i', 'o', 'l']) && has_pairs($passwd, 2);
}
assert(!valid_passwd('hijklmmn'));
assert(!valid_passwd('abbceffg'));
assert(!valid_passwd('abbcegjk'));
assert(valid_passwd('abcdffaa'));
assert(valid_passwd('abcdffaa'));

do {
    $input = incpass($input);
} while (!valid_passwd($input));
echo $input;
