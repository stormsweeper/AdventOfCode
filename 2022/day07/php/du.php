<?php

$input = fopen($argv[1], 'r');

$dirs = $files = [];
$cwd = '';
$mode = 0;

function pathmunge(string $cwd, string $dirarg): string {
    if ($dirarg === '') return $cwd;
    if ($dirarg[0] === '/') return $dirarg;
    if ($dirarg === '..') {
        return parentdir($cwd) ?: '/';
    }
    if ($cwd === '/') return '/' . $dirarg;
    return $cwd . '/' . $dirarg;
}
assert(pathmunge('/foo', '') === '/foo');
assert(pathmunge('/foo', '/bar') === '/bar');
assert(pathmunge('/foo', '..') === '/');
assert(pathmunge('/foo', 'bar') === '/foo/bar');
assert(pathmunge('/', 'foo') === '/foo');

// normally dirname('/') returns '/', wrapping for ease
function parentdir(string $dir): string {
    if ($dir === '/') return '';
    return dirname($dir);
}
assert(parentdir('/') === '');
assert(parentdir('/foo') === '/');
assert(parentdir('/foo/bar') === '/foo');


while (($line = fgets($input)) !== false) {
    $line = trim($line);
    if (!$line) break;

    // handle cd
    if (strpos($line, '$ cd ') === 0) {
        $cwd = pathmunge($cwd, substr($line, 5));
        $dirs[$cwd] = $dirs[$cwd] ?? 0;
        continue;
    }

    // handle ls
    if (strpos($line, '$ ls') === 0) continue;
    
    // dirs
    if (strpos($line, 'dir ') === 0) {
        $dir = pathmunge($cwd, substr($line, 4));
        $dirs[$dir] = $dirs[$dir] ?? 0;
        continue;
    }

    // files
    [$fsize, $fname] = explode(' ', $line);
    $fsize = intval($fsize);
    $fpath = pathmunge($cwd, $fname);
    $files[$fpath] = $fsize;
    while ($fpath = parentdir($fpath)) {
        $dirs[$fpath] += $fsize;
    }
}


// p1
$candidates = array_filter(
    $dirs,
    function($dsize) { return $dsize <= 100000;}
);

echo array_sum($candidates);