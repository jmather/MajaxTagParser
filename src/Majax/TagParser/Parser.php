<?php

namespace Majax\TagParser;

abstract class Parser {
    public $input;     // from where do we get tokens?

    /** @var \Majax\TagParser\Token */
    public $current; // the current lookahead token
    public $next;

    public function __construct(Lexer $input) {
        $this->input = $input;
        $this->consume();
    }

    public function consume() {
        if ($this->current == null)
            $this->current = $this->input->nextToken();
        else
            $this->current = $this->next;
        $this->next = $this->input->nextToken();

        echo 'Current: '.$this->current."\r\n";
    }
}
