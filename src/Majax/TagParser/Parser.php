<?php

namespace Majax\TagParser;

abstract class Parser {
    public $input;     // from where do we get tokens?

    /** @var \Majax\TagParser\Token */
    public $current; // the current lookahead token
    public $next;

    public function setInput(Lexer $input)
    {
        $this->input = $input;
        $this->current = null;
        $this->next = null;
        if ($this->input != null)
            $this->consume();
    }

    public function consume() {
        if ($this->current == null)
            $this->current = $this->input->nextToken();
        else
            $this->current = $this->next;
        $this->next = $this->input->nextToken();
    }
}
