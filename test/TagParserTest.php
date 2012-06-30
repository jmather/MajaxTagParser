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

    public function testForBugFoundInVideo()
    {
        $d = array(
            new Token(TagLexer::LPAREN, '(', 'LPAREN'),
            new Token(TagLexer::NAME, 'org1', 'NAME'),
            new Token(TagLexer::PLUS, '+', 'PLUS'),
            new Token(TagLexer::NAME, 'org2', 'NAME'),
            new Token(TagLexer::RPAREN, ')', 'RPAREN'),
            new Token(TagLexer::MINUS, '-', 'MINUS'),
            new Token(TagLexer::NAME, 'org1', 'NAME'),
            new Token(TagLexer::EOF_TYPE, '<EOF>', 'EOF_TYPE')
        );

        $lexer = $this->getMock('Majax\TagParser\TagLexer');
        $lexer->expects($this->any())
            ->method('nextToken')
            ->will($this->onConsecutiveCalls($d[0], $d[1], $d[2], $d[3], $d[4], $d[5], $d[6], $d[7]));

        $output = $this->parser->process($lexer);

        $expects = new Group();
        $g = new Group($expects);
        $g->addObject(new Tag('org1'));
        $g->addObject(new Tag('org2'));
        $expects->addObject($g);
        $expects->addObject(new Tag('org1'), 'subtract');

        $this->assertEquals($expects, $output);
    }
}