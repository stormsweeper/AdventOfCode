<?php

$diskmap = trim(file_get_contents($argv[1]));

class FreeSpace {
    function __construct(public int $size) {}
    function checksum(int $pos): int { return 0; }
}

class FileData {
    function __construct(public int $id, public int $size) {}
    function checksum(int $pos): int {
        $sum = 0;
        for ($i = 0; $i < $this->size; $i++) {
            $sum += ($pos + $i) * $this->id;
        }
        return $sum;
    }
}

class ElfDisk {
    private $disk = [];

    function __construct(string $diskmap) {
        $disk = [];
        $len = strlen($diskmap);

        $pos = 0;
        for ($i = 0; $i < $len; $i++) {
            $size = intval($diskmap[$i]);
            if ($i%2 === 0) {
                $disk[$pos] = new FileData($i/2, $size);
            } else {
                $disk[$pos] = new FreeSpace($size);
            }
            $pos += $size;
        }

        $this->disk = $disk;
    }

    function first_free(): int {
        foreach ($this->disk as $pos => $block) {
            if ($block instanceof FreeSpace) return $pos;
        }

        return -1;
    }

    function last_filedata(): int {
        foreach (array_reverse(array_keys($this->disk)) as $pos) {
            $block = $this->disk[$pos];
            if ($block instanceof FileData) return $pos;
        }

        return -1;
    }

    function fraggable(): bool {
        return $this->first_free() < $this->last_filedata();
    }

    function checksum(): int {
        $sum = 0;

        foreach ($this->disk as $pos => $block) {
            $sum += $block->checksum($pos);
        }

        return $sum;
    }

    function frag(): void {
        if (!$this->fraggable()) return;

        $pos_free = $this->first_free();
        $free = $this->disk[$pos_free];
        unset($this->disk[$pos_free]);

        $pos_data = $this->last_filedata();
        $file = $this->disk[$pos_data];
        unset($this->disk[$pos_data]);

        if ($free->size === $file->size) {
            $this->disk[$pos_free] = $file;
        } elseif ($free->size > $file->size) {
            $this->disk[$pos_free] = $file;
            $this->disk[$pos_free + $file->size] = new FreeSpace($free->size - $file->size);
        } else {
            $this->disk[$pos_free] = new FileData($file->id, $free->size);
            $this->disk[$pos_data] = new FileData($file->id, $file->size - $free->size);
        }

        ksort($this->disk);
    }
}

$disk = new ElfDisk($diskmap);


while ($disk->fraggable()) $disk->frag();

echo $disk->checksum();

