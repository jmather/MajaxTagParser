<?php

use \Majax\TagParser\Token;
use \Majax\TagParser\TagLexer;

class LexerTest extends PHPUnit_Framework_TestCase
{
    /** @var \Majax\TagParser\TagLexer */
    private $lexer;

    public function setup()
    {
        $this->lexer = new \Majax\TagParser\TagLexer();
    }
    public function testBasicValidString()
    {
        $string = 'a + b';

        $expects = array(
            new Token(TagLexer::NAME, 'a', 'NAME'),
            new Token(TagLexer::PLUS, '+', 'PLUS'),
            new Token(TagLExer::NAME, 'b', 'NAME'),
            new Token(TagLexer::EOF_TYPE, '<EOF>', 'EOF_TYPE')
        );

        $this->lexer->setInput($string);

        $actual = array();

        $token = $this->lexer->nextToken();
        $actual[] = $token;
        while ($token->type != 1) {
            $token = $this->lexer->nextToken();
            $actual[] = $token;
        }

        $this->assertEquals($expects, $actual);
    }

    public function testAllValidSyntaxLexes()
    {
        $string = 'a + (c|d - e^f) ';

        $expects = array(
            new Token(TagLexer::NAME, 'a', 'NAME'),
            new Token(TagLexer::PLUS, '+', 'PLUS'),
            new Token(TagLexer::LPAREN, '(', 'LPAREN'),
            new Token(TagLexer::NAME, 'c', 'NAME'),
            new Token(TagLexer::LIMIT, '|', 'LIMIT'),
            new Token(TagLexer::NAME, 'd', 'NAME'),
            new Token(TagLexer::MINUS, '-', 'MINUS'),
            new Token(TagLexer::NAME, 'e', 'NAME'),
            new Token(TagLexer::EXCLUDE, '^', 'EXCLUDE'),
            new Token(TagLexer::NAME, 'f', 'NAME'),
            new Token(TagLexer::RPAREN, ')', 'RPAREN'),
            new Token(TagLexer::EOF_TYPE, '<EOF>', 'EOF_TYPE')
        );

        $this->lexer->setInput($string);

        $actual = array();

        $token = $this->lexer->nextToken();
        $actual[] = $token;
        while ($token->type != 1) {
            $token = $this->lexer->nextToken();
            $actual[] = $token;
        }

        $this->assertEquals($expects, $actual);
    }

}