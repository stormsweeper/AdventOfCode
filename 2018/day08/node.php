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

    public function nodeValue() {
        if (empty($this->children)) {
            return array_sum($this->metadata);
        }

        $child_values = array_map(
            function($ref) {
                $pos = $ref - 1;
                if (!isset($this->children[$pos])) {
                    return 0;
                }

                return ($this->children[$pos])->nodeValue();
            },
            $this->metadata
        );
        return array_sum($child_values);
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

echo "metasum: {$tree->metaSum()}\nnode value: {$tree->nodeValue()}\n";

