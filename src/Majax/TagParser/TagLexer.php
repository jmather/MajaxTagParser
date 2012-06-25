<?php

namespace Majax\TagParser;

class TagLexer extends Lexer {
    const EOF       = -1; // represent end of file char
    const EOF_TYPE  = 1;  // represent EOF token type
    const NAME      = 2;
    const LPAREN    = 3;
    const RPAREN    = 4;
    const PLUS      = 5;
    const MINUS     = 6;
    const LIMIT     = 7;
    const EXCLUDE     = 8;
    static $tokenNames = array("n/a", "<EOF>",
        "NAME",
        "LPAREN", "RPAREN",
        "PLUS", "MINUS",
        "LIMIT", "EXCLUDE");

    public function getTokenName($x) {
        return self::$tokenNames[$x];
    }

    public function isLETTER() {
        return $this->c >= 'a' &&
            $this->c <= 'z' ||
            $this->c >= 'A' &&
                $this->c <= 'Z';
    }

    public function nextToken() {
        while ( $this->c != self::EOF ) {
            switch ( $this->c ) {
                case ' ' :  case '\t': case '\n': case '\r': $this->WS();
                    continue;
                case '(' :
                    $this->consume();
                    $type = self::LPAREN;
                    return new Token($type, "(", self::$tokenNames[$type]);
                case ')' :
                    $this->consume();
                    $type = self::RPAREN;
                    return new Token($type, ")", self::$tokenNames[$type]);
                case '+' :
                    $this->consume();
                    $type = self::PLUS;
                    return new Token($type, "+", self::$tokenNames[$type]);
                case '-' :
                    $this->consume();
                    $type = self::MINUS;
                    return new Token($type, "-", self::$tokenNames[$type]);
                case '|' :
                    $this->consume();
                    $type = self::LIMIT;
                    return new Token($type, "|", self::$tokenNames[$type]);
                case '^' :
                    $this->consume();
                    $type = self::EXCLUDE;
                    return new Token($type, "^", self::$tokenNames[$type]);
                default:
                    if ($this->isLETTER() ) return $this->NAME();
                    throw new \Exception("invalid character: " + $this->c);
            }
        }
        return new Token(self::EOF_TYPE,"<EOF>", 'EOF_TYPE');
    }

    /** NAME : ('a'..'z'|'A'..'Z')+; // NAME is sequence of >=1 letter */
    public function NAME() {
        $buf = '';
        do {
            $buf .= $this->c;
            $this->consume();
        } while ($this->isLETTER());

        return new Token(self::NAME, $buf, 'NAME');
    }

    /** WS : (' '|'\t'|'\n'|'\r')* ; // ignore any whitespace */
    public function WS() {
        while(ctype_space($this->c)) {
            $this->consume();
        }
    }
}
