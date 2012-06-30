<?php

namespace Majax\TagParser;

class TagParser extends Parser {
    private $data;

    /** @var \Majax\TagParser\Result\Group */
    private $current_group;

    /** @var \Majax\TagParser\Result\Tag */
    private $last_tag;

    private $ignore_next = false;

    public function setInput(Lexer $input)
    {
        parent::setInput($input);
        $this->data = new Result\Group();
        $this->current_group = $this->data;
        $this->last_tag = null;
    }

    /** list : '[' elements ']' ; // match bracketed list */
    public function process(TagLexer $input) {
        $this->setInput($input);

        while ($this->current->type != TagLexer::EOF_TYPE)
        {
            $this->validateState($this->current, $this->next);

            if ($this->ignore_next == false)
                $this->processData($this->current, $this->next);
            else
                $this->ignore_next = false;

            $this->consume();
        }

        return $this->data;
    }

    public function processData(Token $current_token, Token $next_token = null)
    {
        switch($current_token->type)
        {
            case TagLexer::NAME:
                $t = new Result\Tag($current_token->text);
                $this->last_tag = $t;
                $this->current_group->addObject($t);
                break;
            case TagLexer::RPAREN:
                $this->current_group = $this->current_group->getParent();
                break;
            case TagLexer::LPAREN:
                $g = new Result\Group($this->current_group);
                $this->current_group->addObject($g, 'add');
                $this->current_group = $g;
                break;
            case TagLexer::PLUS:
                if ($next_token->type == TagLexer::NAME)
                {
                    $t = new Result\Tag($next_token->text);
                    $this->current_group->addObject($t, 'add');
                    $this->last_tag = $t;
                    $this->ignore_next = true;
                }
                if ($next_token->type == TagLexer::LPAREN)
                {
                    $g = new Result\Group($this->current_group);
                    $this->current_group->addObject($g, 'add');
                    $this->current_group = $g;
                    $this->ignore_next = true;
                }
                break;
            case TagLexer::MINUS:
                if ($next_token->type == TagLexer::NAME)
                {
                    $t = new Result\Tag($next_token->text);
                    $this->current_group->addObject($t, 'subtract');
                    $this->last_tag = $t;
                    $this->ignore_next = true;
                }
                if ($next_token->type == TagLexer::LPAREN)
                {
                    $g = new Result\Group($this->current_group);
                    $this->current_group->addObject($g, 'subtract');
                    $this->current_group = $g;
                    $this->ignore_next = true;
                }
                break;
            case TagLexer::EXCLUDE:
                $t = new Result\Tag($next_token->text);
                $this->last_tag->addExclude($t);
                $this->ignore_next = true;
                break;
            case TagLexer::LIMIT:
                $t = new Result\Tag($next_token->text);
                $this->last_tag->addLimit($t);
                $this->ignore_next = true;
                break;
        }
    }

    public function validateState(Token $current_token, Token $next_token = null)
    {
        if ($current_token->type == TagLexer::EOF_TYPE)
        {
            if ($next_token != null)
                throw new \UnexpectedValueException(
                    'Expecting no input after EOF : Found '.$next_token
                );
        }
        if ($current_token->type == TagLexer::NAME)
        {
            $allowed_next = array(
                TagLexer::EXCLUDE,
                TagLexer::LIMIT,
                TagLexer::PLUS,
                TagLexer::MINUS,
                TagLexer::RPAREN,
                TagLexer::EOF_TYPE
            );

            if (!in_array($next_token->type, $allowed_next))
            {
                throw new \UnexpectedValueException(
                    'Expecting |, ^, -, +, or ) after '.$current_token.' : Found '.$next_token
                );
            }
        }
        if ($current_token->type == TagLexer::MINUS || $current_token->type == TagLexer::PLUS)
        {
            $allowed_next = array(
                TagLexer::LPAREN,
                TagLexer::NAME
            );


            if (!in_array($next_token->type, $allowed_next))
            {
                throw new \UnexpectedValueException(
                    'Expecting ( or TAG after '.$current_token.' : Found '.$next_token
                );
            }
        }
        if ($current_token->type == TagLexer::LIMIT || $current_token->type == TagLexer::EXCLUDE)
        {
            $allowed_next = array(
                TagLexer::NAME,

            );
            if (!in_array($next_token->type, $allowed_next))
                throw new \UnexpectedValueException(
                    'Expecting TAG after '.$current_token.' : Found '.$next_token
                );
        }
        if ($current_token->type == TagLexer::LPAREN)
        {
            if ($next_token->type != TagLexer::NAME)
            {
                throw new \UnexpectedValueException(
                    'Expecting TAG after '.$current_token.' : Found '.$next_token
                );
            }
        }
        if ($current_token->type == TagLexer::RPAREN)
        {
            $allowed_next = array(
                TagLexer::PLUS,
                TagLexer::MINUS,
                TagLexer::EOF_TYPE,
                );
            if (!in_array($next_token->type, $allowed_next) && $next_token != null)
            {
                throw new \UnexpectedValueException(
                    'Expecting +, -, or end of line after '.$current_token.' : Found '.$next_token
                );
            }
        }
    }
}
