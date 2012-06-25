<?php

use \Majax\TagParser\Token;
use \Majax\TagParser\TagParser;
use \Majax\TagParser\TagLexer;
use \Majax\TagParser\Result\Group;
use \Majax\TagParser\Result\Tag;


class TagParserTest extends PHPUnit_Framework_TestCase
{
    /** @var $parser \Majax\TagParser\TagParser */
    private $parser;

    public function setup()
    {
        $this->parser = new \Majax\TagParser\TagParser();
    }

    public function testValidParse()
    {
        $s = array(
            new Token(TagLexer::NAME, 'a', 'NAME'),
            new Token(TagLexer::PLUS, '+', 'PLUS'),
            new Token(TagLExer::NAME, 'b', 'NAME'),
            new Token(TagLexer::EOF_TYPE, '<EOF>', 'EOF_TYPE'),
        );

        $lexer = $this->getMock('Majax\TagParser\TagLexer');
        $lexer->expects($this->any())
            ->method('nextToken')
            ->will($this->onConsecutiveCalls($s[0], $s[1], $s[2], $s[3]));

        $output = $this->parser->process($lexer);

        $expects = new Group();
        $expects->addObject(new Tag('a'));
        $expects->addObject(new Tag('b'));

        $this->assertEquals($expects, $output);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testInvalidParse()
    {
        $s = array(
            new Token(TagLexer::NAME, 'a', 'NAME'),
            new Token(TagLExer::NAME, 'b', 'NAME'),
            new Token(TagLexer::EOF_TYPE, '<EOF>', 'EOF_TYPE'),
        );

        $lexer = $this->getMock('Majax\TagParser\TagLexer');
        $lexer->expects($this->any())
            ->method('nextToken')
            ->will($this->onConsecutiveCalls($s[0], $s[1], $s[2]));

        $output = $this->parser->process($lexer);

        $expects = new Group();
        $expects->addObject(new Tag('a'));
        $expects->addObject(new Tag('b'));

        $this->assertEquals($expects, $output);
    }
}