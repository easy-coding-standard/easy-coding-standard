<?php

declare (strict_types=1);
namespace ECSPrefix20210803\TheSeer\Tokenizer;

class Tokenizer
{
    /**
     * Token Map for "non-tokens"
     *
     * @var array
     */
    private $map = ['(' => 'T_OPEN_BRACKET', ')' => 'T_CLOSE_BRACKET', '[' => 'T_OPEN_SQUARE', ']' => 'T_CLOSE_SQUARE', '{' => 'T_OPEN_CURLY', '}' => 'T_CLOSE_CURLY', ';' => 'T_SEMICOLON', '.' => 'T_DOT', ',' => 'T_COMMA', '=' => 'T_EQUAL', '<' => 'T_LT', '>' => 'T_GT', '+' => 'T_PLUS', '-' => 'T_MINUS', '*' => 'T_MULT', '/' => 'T_DIV', '?' => 'T_QUESTION_MARK', '!' => 'T_EXCLAMATION_MARK', ':' => 'T_COLON', '"' => 'T_DOUBLE_QUOTES', '@' => 'T_AT', '&' => 'T_AMPERSAND', '%' => 'T_PERCENT', '|' => 'T_PIPE', '$' => 'T_DOLLAR', '^' => 'T_CARET', '~' => 'T_TILDE', '`' => 'T_BACKTICK'];
    public function parse(string $source) : \ECSPrefix20210803\TheSeer\Tokenizer\TokenCollection
    {
        $result = new \ECSPrefix20210803\TheSeer\Tokenizer\TokenCollection();
        if ($source === '') {
            return $result;
        }
        $tokens = \token_get_all($source);
        $lastToken = new \ECSPrefix20210803\TheSeer\Tokenizer\Token($tokens[0][2], 'Placeholder', '');
        foreach ($tokens as $pos => $tok) {
            if (\is_string($tok)) {
                $token = new \ECSPrefix20210803\TheSeer\Tokenizer\Token($lastToken->getLine(), $this->map[$tok], $tok);
                $result->addToken($token);
                $lastToken = $token;
                continue;
            }
            $line = $tok[2];
            $values = \preg_split('/\\R+/Uu', $tok[1]);
            foreach ($values as $v) {
                $token = new \ECSPrefix20210803\TheSeer\Tokenizer\Token($line, \token_name($tok[0]), $v);
                $lastToken = $token;
                $line++;
                if ($v === '') {
                    continue;
                }
                $result->addToken($token);
            }
        }
        return $this->fillBlanks($result, $lastToken->getLine());
    }
    private function fillBlanks(\ECSPrefix20210803\TheSeer\Tokenizer\TokenCollection $tokens, int $maxLine) : \ECSPrefix20210803\TheSeer\Tokenizer\TokenCollection
    {
        $prev = new \ECSPrefix20210803\TheSeer\Tokenizer\Token(0, 'Placeholder', '');
        $final = new \ECSPrefix20210803\TheSeer\Tokenizer\TokenCollection();
        foreach ($tokens as $token) {
            if ($prev === null) {
                $final->addToken($token);
                $prev = $token;
                continue;
            }
            $gap = $token->getLine() - $prev->getLine();
            while ($gap > 1) {
                $linebreak = new \ECSPrefix20210803\TheSeer\Tokenizer\Token($prev->getLine() + 1, 'T_WHITESPACE', '');
                $final->addToken($linebreak);
                $prev = $linebreak;
                $gap--;
            }
            $final->addToken($token);
            $prev = $token;
        }
        $gap = $maxLine - $prev->getLine();
        while ($gap > 0) {
            $linebreak = new \ECSPrefix20210803\TheSeer\Tokenizer\Token($prev->getLine() + 1, 'T_WHITESPACE', '');
            $final->addToken($linebreak);
            $prev = $linebreak;
            $gap--;
        }
        return $final;
    }
}
