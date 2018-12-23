<?php
$reg0 = intval($argv[2] ?? 0);
$REG = [$reg0, 0, 0, 0, 0, 0];
$ops = 0;

#ip 2
#L00: seti 123 0 3
L00: $REG[3] = 123;
#L01: bani 3 456 3
L01:
// triggers an inf loop (-> L4 -> L1 ->L4 ...)
if ( $REG[3]%128 !== 123 ) {
    echo "inf loop detected for start {$reg0}";
    print_r($REG);
    exit;
}
$REG[3] = $REG[3] & 456;

#L02: eqri 3 72 3
L02: if ($REG[3] === 72) { // true on first pass
    echo "pass L02 ({$REG[3]} === 72)\n";
    $REG[3] = 1;
    #L03: addr 3 2 2
    goto L05; // 1 + 3 + 1
} else {
    echo "fail L02 ({$REG[3]} === 72)\n";
    $REG[3] = 0;
    #L03: addr 3 2 2
    goto L04; // 0 + 3 + 1
}

#L04: seti 0 0 2
L04: goto L01;

#L05: seti 0 0 3
L05: $REG[3] = 0;
#L06: bori 3 65536 1
L06: $REG[1] = $REG[3] | 65536; // first pass $REG[1] will be 0, this will eval to 65536 
#L07: seti 4921097 0 3
L07: $REG[3] = 4921097;
#L08: bani 1 255 4
L08: $REG[4] = $REG[1] & 255; // first pass this will eval to 0, 65536 is pow(2, 16)
#L09: addr 3 4 3
L09: $REG[3] = $REG[3] + $REG[4]; // first pass this will eval to 4921097
#L10: bani 3 16777215 3
L10: $REG[3] = $REG[3] & 16777215; // first pass this will eval to 4921097, 16777215 is 24 1s in binary
#L11: muli 3 65899 3
L11: $REG[3] = $REG[3] * 65899; // first pass this will be 324295371203
#L12: bani 3 16777215 3
L12: $REG[3] = $REG[3] & 16777215; // first pass this will be 8563139

#L13: gtir 256 1 4
L13: if (256 > $REG[1]) { // will be false first pass
    echo "pass L13 (256 > {$REG[1]})\n";
    $REG[4] = 1;
    #L14: addr 4 2 2
    goto L16; // 6 ops
} else {
    echo "fail L13 (256 > {$REG[1]})\n";
    $REG[4] = 0;
    #L14: addr 4 2 2
    goto L15;
}

#L15: addi 2 1 2
L15: goto L17;

#L16: seti 27 8 2
L16: goto L28; // 1 ops

#L17: seti 0 5 4
L17: $REG[4] = 0;
#L18: addi 4 1 5
L18: $REG[5] = $REG[4] + 1;
#L19: muli 5 256 5
L19: $REG[5] = $REG[5] * 256;
#L20: gtrr 5 1 5
L20: if ($REG[5] > $REG[1]) {
    echo "pass L20 ({$REG[5]} > {$REG[1]})\n";
    $REG[5] = 1;
    #L21: addr 5 2 2
    goto L23;
} else {
    echo "false L20 ({$REG[5]} > {$REG[1]})\n";
    $REG[5] = 0;
    #L21: addr 5 2 2
    goto L22;
}

#L22: addi 2 1 2
L22: goto L24;

#L23: seti 25 1 2
L23: goto L26;

#L24: addi 4 1 4
L24: $REG[4] = $REG[4] + 1;
#L25: seti 17 8 2
L25: goto L18;

#L26: setr 4 3 1
L26: $REG[1] = $REG[4];
#L27: seti 7 9 2
L27: goto L08;

#L28: eqrr 3 0 4
L28:
echo "L28";print_r($REG); exit;
if ($REG[3] === $REG[0]) {
    echo "pass L28 ({$REG[3]} === {$REG[0]})\n";
    $REG[4] = 1;
    #L29: addr 4 2 2
    goto HALT; // 31
} else {
    echo "fail L28 ({$REG[3]} === {$REG[0]})\n";
    $REG[4] = 0;
    #L29: addr 4 2 2
    goto L30;
}

#L30: seti 5 4 2
L30: goto L06;

HALT:
echo "HALT";
print_r($REG);
echo $ops;
exit;









