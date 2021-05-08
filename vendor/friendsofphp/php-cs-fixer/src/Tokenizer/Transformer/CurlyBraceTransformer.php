<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Tokenizer\Transformer;

use PhpCsFixer\Tokenizer\AbstractTransformer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * Transform discriminate overloaded curly braces tokens.
 *
 * Performed transformations:
 * - closing `}` for T_CURLY_OPEN into CT::T_CURLY_CLOSE,
 * - closing `}` for T_DOLLAR_OPEN_CURLY_BRACES into CT::T_DOLLAR_CLOSE_CURLY_BRACES,
 * - in `$foo->{$bar}` into CT::T_DYNAMIC_PROP_BRACE_OPEN and CT::T_DYNAMIC_PROP_BRACE_CLOSE,
 * - in `${$foo}` into CT::T_DYNAMIC_VAR_BRACE_OPEN and CT::T_DYNAMIC_VAR_BRACE_CLOSE,
 * - in `$array{$index}` into CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN and CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE,
 * - in `use some\a\{ClassA, ClassB, ClassC as C}` into CT::T_GROUP_IMPORT_BRACE_OPEN, CT::T_GROUP_IMPORT_BRACE_CLOSE.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class CurlyBraceTransformer extends \PhpCsFixer\Tokenizer\AbstractTransformer
{
    /**
     * {@inheritdoc}
     * @return int
     */
    public function getRequiredPhpVersionId()
    {
        return 50000;
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param int $index
     */
    public function process(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, $index)
    {
        $this->transformIntoCurlyCloseBrace($tokens, $token, $index);
        $this->transformIntoDollarCloseBrace($tokens, $token, $index);
        $this->transformIntoDynamicPropBraces($tokens, $token, $index);
        $this->transformIntoDynamicVarBraces($tokens, $token, $index);
        $this->transformIntoCurlyIndexBraces($tokens, $token, $index);
        if (\PHP_VERSION_ID >= 70000) {
            $this->transformIntoGroupUseBraces($tokens, $token, $index);
        }
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function getCustomTokens()
    {
        return [\PhpCsFixer\Tokenizer\CT::T_CURLY_CLOSE, \PhpCsFixer\Tokenizer\CT::T_DOLLAR_CLOSE_CURLY_BRACES, \PhpCsFixer\Tokenizer\CT::T_DYNAMIC_PROP_BRACE_OPEN, \PhpCsFixer\Tokenizer\CT::T_DYNAMIC_PROP_BRACE_CLOSE, \PhpCsFixer\Tokenizer\CT::T_DYNAMIC_VAR_BRACE_OPEN, \PhpCsFixer\Tokenizer\CT::T_DYNAMIC_VAR_BRACE_CLOSE, \PhpCsFixer\Tokenizer\CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN, \PhpCsFixer\Tokenizer\CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE, \PhpCsFixer\Tokenizer\CT::T_GROUP_IMPORT_BRACE_OPEN, \PhpCsFixer\Tokenizer\CT::T_GROUP_IMPORT_BRACE_CLOSE];
    }
    /**
     * Transform closing `}` for T_CURLY_OPEN into CT::T_CURLY_CLOSE.
     *
     * This should be done at very beginning of curly braces transformations.
     * @return void
     * @param int $index
     */
    private function transformIntoCurlyCloseBrace(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, $index)
    {
        if (!$token->isGivenKind(\T_CURLY_OPEN)) {
            return;
        }
        $level = 1;
        $nestIndex = $index;
        while (0 < $level) {
            ++$nestIndex;
            // we count all kind of {
            if ($tokens[$nestIndex]->equals('{')) {
                ++$level;
                continue;
            }
            // we count all kind of }
            if ($tokens[$nestIndex]->equals('}')) {
                --$level;
            }
        }
        $tokens[$nestIndex] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_CURLY_CLOSE, '}']);
    }
    /**
     * @return void
     * @param int $index
     */
    private function transformIntoDollarCloseBrace(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, $index)
    {
        if ($token->isGivenKind(\T_DOLLAR_OPEN_CURLY_BRACES)) {
            $nextIndex = $tokens->getNextTokenOfKind($index, ['}']);
            $tokens[$nextIndex] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_DOLLAR_CLOSE_CURLY_BRACES, '}']);
        }
    }
    /**
     * @return void
     * @param int $index
     */
    private function transformIntoDynamicPropBraces(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, $index)
    {
        if (!$token->isObjectOperator()) {
            return;
        }
        if (!$tokens[$index + 1]->equals('{')) {
            return;
        }
        $openIndex = $index + 1;
        $closeIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $openIndex);
        $tokens[$openIndex] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_DYNAMIC_PROP_BRACE_OPEN, '{']);
        $tokens[$closeIndex] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_DYNAMIC_PROP_BRACE_CLOSE, '}']);
    }
    /**
     * @return void
     * @param int $index
     */
    private function transformIntoDynamicVarBraces(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, $index)
    {
        if (!$token->equals('$')) {
            return;
        }
        $openIndex = $tokens->getNextMeaningfulToken($index);
        if (null === $openIndex) {
            return;
        }
        $openToken = $tokens[$openIndex];
        if (!$openToken->equals('{')) {
            return;
        }
        $closeIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $openIndex);
        $tokens[$openIndex] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_DYNAMIC_VAR_BRACE_OPEN, '{']);
        $tokens[$closeIndex] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_DYNAMIC_VAR_BRACE_CLOSE, '}']);
    }
    /**
     * @return void
     * @param int $index
     */
    private function transformIntoCurlyIndexBraces(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, $index)
    {
        if (!$token->equals('{')) {
            return;
        }
        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        if (!$tokens[$prevIndex]->equalsAny([[\T_STRING], [\T_VARIABLE], [\PhpCsFixer\Tokenizer\CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE], ']', ')'])) {
            return;
        }
        if ($tokens[$prevIndex]->isGivenKind(\T_STRING) && !$tokens[$tokens->getPrevMeaningfulToken($prevIndex)]->isObjectOperator()) {
            return;
        }
        if ($tokens[$prevIndex]->equals(')') && !$tokens[$tokens->getPrevMeaningfulToken($tokens->findBlockStart(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $prevIndex))]->isGivenKind(\T_ARRAY)) {
            return;
        }
        $closeIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_ARRAY_INDEX_CURLY_BRACE_OPEN, '{']);
        $tokens[$closeIndex] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE, '}']);
    }
    /**
     * @return void
     * @param int $index
     */
    private function transformIntoGroupUseBraces(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, $index)
    {
        if (!$token->equals('{')) {
            return;
        }
        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        if (!$tokens[$prevIndex]->isGivenKind(\T_NS_SEPARATOR)) {
            return;
        }
        $closeIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_GROUP_IMPORT_BRACE_OPEN, '{']);
        $tokens[$closeIndex] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_GROUP_IMPORT_BRACE_CLOSE, '}']);
    }
}
