<?php

// wrapper to handle parsing the stream
class BITSStream {
    private int $length = 0;
    private int $cursor = 0;

    public function __construct(private string $stream = '', bool $is_hex = true) {
        if ($is_hex) {
            $binstring = '';
            $hex_len = strlen($stream);
            for ($i = 0; $i < $hex_len; $i += 2) {
                $bin = base_convert(substr($stream, $i, 2), 16, 2);
                $binstring .= str_pad($bin, 8, '0', STR_PAD_LEFT);
            }
            $this->stream = $binstring;
        }
        $this->length = strlen($this->stream);
    }

    public function isEOS(): bool { return $this->cursor === $this->length; }

    public function read(int $len): string|false {
        if ($this->cursor + $len > $this->length) {
            $this->cursor = $this->length;
            return false;
        }

        $chunk = substr($this->stream, $this->cursor, $len);
        $this->cursor += $len;
        return $chunk;
    }

    public function nextPacket(): BITSPacket|false {
        $start = $this->cursor;
        $version = $this->read(3);
        if ($version === false) return false;
        $version = bindec($version);
        $type = $this->read(3);
        if ($type === false) return false;
        $type = bindec($type);

        if ($type === 4) {
            // literal payload
            $payload = $this->readLiteralPayload();
        }
        else {
            // operator payload
            $payload = $this->readOperatorPayload();
        }
        if ($payload === false) return false;

        $length = $this->cursor - $start;
        return new BITSPacket($version, $type, $length, $payload);
    }

    private function readLiteralPayload(): string|false {
        $p = '';
        do {
            $chunk = $this->read(5);
            if ($chunk === false) return false;
            $p .= substr($chunk, 1);
        } while ($chunk[0] === '1');
        return $p;
    }

    private function readOperatorPayload(): array|false {
        $ltype = $this->read(1);
        if ($ltype === false) return false;

        // this is x bits of data to decode
        if ($ltype === '0') {
            $plen = $this->read(15);
            if ($plen === false) return false;
            $plen = bindec($plen);

            $subdata = $this->read($plen);
            if ($subdata === false) return false;
            $substream = new BITSStream($subdata, false);
            $payload = [];
            while (!$substream->isEOS()) {
                $p = $substream->nextPacket();
                if ($p !== false) $payload[] = $p;
            }
            return $payload;
        }

        // this is the number of packets to read from the stream
        $plen = $this->read(11);
        if ($plen === false) return false;
        $plen = bindec($plen);

        $payload = [];
        while (count($payload) < $plen) {
            $p = $this->nextPacket();
            if ($p === false) return false;
            $payload[] = $p;
        }
        return $payload;
    }
}

// POPO representing a packet
class BITSPacket {
    public function __construct(public int $version, public int $type, public int $length, public array|string $payload) {}

    public function isLiteral(): bool { return $this->type === 4; }
    public function isOperator(): bool { return $this->type !== 4; }

    public function versionSum(): int {
        $total = $this->version;
        if ($this->isOperator()) {
            foreach ($this->payload as $subpacket) $total += $subpacket->versionSum();
        }
        return $total;
    }
}


// // first example, literal packet with 2021 as a value
// $hex = 'D2FE28';

// // second example, operator packet with two literal subpackets
// $hex = '38006F45291200';

// // third example, 3 literal sub packets
// $hex = 'EE00D40C823060';

// // first sum example, should be 16
// $hex = '8A004A801A8002F478';

$hex = trim(file_get_contents($argv[1]));

$stream = new BITSStream($hex);

$packet = $stream->nextPacket();

echo $packet->versionSum();