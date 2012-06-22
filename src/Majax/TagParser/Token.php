<?php

namespace Majax\TagParser;

class Token {
    public $type;
    public $text;
    public $name;

    public function __construct($type, $text, $name) {
        $this->type = $type;
        $this->text = $text;
        $this->name = $name;
    }

    public function __toString() {
        return "<'" . $this->text . "'," . $this->name . ">";
    }
}
