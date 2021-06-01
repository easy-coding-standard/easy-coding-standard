<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ConfigTransformer20210601\Symfony\Component\ExpressionLanguage;

/**
 * Lexes an expression.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Lexer
{
    /**
     * Tokenizes an expression.
     *
     * @return TokenStream A token stream instance
     *
     * @throws SyntaxError
     */
    public function tokenize(string $expression)
    {
        $expression = \str_replace(["\r", "\n", "\t", "\v", "\f"], ' ', $expression);
        $cursor = 0;
        $tokens = [];
        $brackets = [];
        $end = \strlen($expression);
        while ($cursor < $end) {
            if (' ' == $expression[$cursor]) {
                ++$cursor;
                continue;
            }
            if (\preg_match('/[0-9]+(?:\\.[0-9]+)?([Ee][\\+\\-][0-9]+)?/A', $expression, $match, 0, $cursor)) {
                // numbers
                $number = (float) $match[0];
                // floats
                if (\preg_match('/^[0-9]+$/', $match[0]) && $number <= \PHP_INT_MAX) {
                    $number = (int) $match[0];
                    // integers lower than the maximum
                }
                $tokens[] = new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token(\ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token::NUMBER_TYPE, $number, $cursor + 1);
                $cursor += \strlen($match[0]);
            } elseif (\false !== \strpos('([{', $expression[$cursor])) {
                // opening bracket
                $brackets[] = [$expression[$cursor], $cursor];
                $tokens[] = new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token(\ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token::PUNCTUATION_TYPE, $expression[$cursor], $cursor + 1);
                ++$cursor;
            } elseif (\false !== \strpos(')]}', $expression[$cursor])) {
                // closing bracket
                if (empty($brackets)) {
                    throw new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\SyntaxError(\sprintf('Unexpected "%s".', $expression[$cursor]), $cursor, $expression);
                }
                list($expect, $cur) = \array_pop($brackets);
                if ($expression[$cursor] != \strtr($expect, '([{', ')]}')) {
                    throw new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\SyntaxError(\sprintf('Unclosed "%s".', $expect), $cur, $expression);
                }
                $tokens[] = new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token(\ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token::PUNCTUATION_TYPE, $expression[$cursor], $cursor + 1);
                ++$cursor;
            } elseif (\preg_match('/"([^"\\\\]*(?:\\\\.[^"\\\\]*)*)"|\'([^\'\\\\]*(?:\\\\.[^\'\\\\]*)*)\'/As', $expression, $match, 0, $cursor)) {
                // strings
                $tokens[] = new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token(\ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token::STRING_TYPE, \stripcslashes(\substr($match[0], 1, -1)), $cursor + 1);
                $cursor += \strlen($match[0]);
            } elseif (\preg_match('/(?<=^|[\\s(])not in(?=[\\s(])|\\!\\=\\=|(?<=^|[\\s(])not(?=[\\s(])|(?<=^|[\\s(])and(?=[\\s(])|\\=\\=\\=|\\>\\=|(?<=^|[\\s(])or(?=[\\s(])|\\<\\=|\\*\\*|\\.\\.|(?<=^|[\\s(])in(?=[\\s(])|&&|\\|\\||(?<=^|[\\s(])matches|\\=\\=|\\!\\=|\\*|~|%|\\/|\\>|\\||\\!|\\^|&|\\+|\\<|\\-/A', $expression, $match, 0, $cursor)) {
                // operators
                $tokens[] = new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token(\ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token::OPERATOR_TYPE, $match[0], $cursor + 1);
                $cursor += \strlen($match[0]);
            } elseif (\false !== \strpos('.,?:', $expression[$cursor])) {
                // punctuation
                $tokens[] = new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token(\ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token::PUNCTUATION_TYPE, $expression[$cursor], $cursor + 1);
                ++$cursor;
            } elseif (\preg_match('/[a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*/A', $expression, $match, 0, $cursor)) {
                // names
                $tokens[] = new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token(\ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token::NAME_TYPE, $match[0], $cursor + 1);
                $cursor += \strlen($match[0]);
            } else {
                // unlexable
                throw new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\SyntaxError(\sprintf('Unexpected character "%s".', $expression[$cursor]), $cursor, $expression);
            }
        }
        $tokens[] = new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token(\ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\Token::EOF_TYPE, null, $cursor + 1);
        if (!empty($brackets)) {
            list($expect, $cur) = \array_pop($brackets);
            throw new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\SyntaxError(\sprintf('Unclosed "%s".', $expect), $cur, $expression);
        }
        return new \ConfigTransformer20210601\Symfony\Component\ExpressionLanguage\TokenStream($tokens, $expression);
    }
}
