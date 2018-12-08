<?php

class LicenseNode {
    public $children = [];
    public $metadata = [];

    public function metaSum() {
        $child_sums = array_map(
            function($child) { return $child->metaSum(); },
            $this->children
        );
        return array_sum($child_sums) + array_sum($this->metadata);
    }
}

$input = file_get_contents($argv[1]);
$input = array_map('intval', explode(' ', trim($input)));

$current_pos = 0;

function parseNode() {
    global $input, $current_pos;
    $num_children = $input[$current_pos++];
    $num_meta = $input[$current_pos++];
    $node = new LicenseNode();

    while($num_children--) {
        $node->children[] = parseNode();
    }

    while($num_meta--) {
        $node->metadata[] = $input[$current_pos++];
    }

    return $node;
}

$tree = parseNode();

echo $tree->metaSum();

