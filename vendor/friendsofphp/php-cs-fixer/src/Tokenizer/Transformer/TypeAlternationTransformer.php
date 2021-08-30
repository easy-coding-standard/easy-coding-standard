<?php

declare (strict_types=1);
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
 * Transform `|` operator into CT::T_TYPE_ALTERNATION in `function foo(Type1 | Type2 $x) {`
 *                                                    or `} catch (ExceptionType1 | ExceptionType2 $e) {`.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class TypeAlternationTransformer extends \PhpCsFixer\Tokenizer\AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getPriority() : int
    {
        // needs to run after ArrayTypehintTransformer and TypeColonTransformer
        return -15;
    }
    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId() : int
    {
        return 70100;
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param \PhpCsFixer\Tokenizer\Token $token
     * @param int $index
     */
    public function process($tokens, $token, $index) : void
    {
        if (!$token->equals('|')) {
            return;
        }
        $prevIndex = $tokens->getTokenNotOfKindsSibling($index, -1, [\T_CALLABLE, \T_NS_SEPARATOR, \T_STRING, \PhpCsFixer\Tokenizer\CT::T_ARRAY_TYPEHINT, \T_WHITESPACE, \T_COMMENT, \T_DOC_COMMENT]);
        /** @var Token $prevToken */
        $prevToken = $tokens[$prevIndex];
        if ($prevToken->isGivenKind([
            \PhpCsFixer\Tokenizer\CT::T_TYPE_COLON,
            // `:` is part of a function return type `foo(): X|Y`
            \PhpCsFixer\Tokenizer\CT::T_TYPE_ALTERNATION,
            // `|` is part of a union (chain) `X|Y`
            \T_STATIC,
            \T_VAR,
            \T_PUBLIC,
            \T_PROTECTED,
            \T_PRIVATE,
        ])) {
            $this->replaceToken($tokens, $index);
            return;
        }
        if (!$prevToken->equalsAny(['(', ','])) {
            return;
        }
        $prevPrevTokenIndex = $tokens->getPrevMeaningfulToken($prevIndex);
        if ($tokens[$prevPrevTokenIndex]->isGivenKind([\T_CATCH])) {
            $this->replaceToken($tokens, $index);
            return;
        }
        $functionKinds = [[\T_FUNCTION]];
        if (\defined('T_FN')) {
            $functionKinds[] = [\T_FN];
        }
        $functionIndex = $tokens->getPrevTokenOfKind($prevIndex, $functionKinds);
        if (null === $functionIndex) {
            return;
        }
        $braceOpenIndex = $tokens->getNextTokenOfKind($functionIndex, ['(']);
        $braceCloseIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $braceOpenIndex);
        if ($braceCloseIndex < $index) {
            return;
        }
        $this->replaceToken($tokens, $index);
    }
    /**
     * {@inheritdoc}
     */
    public function getCustomTokens() : array
    {
        return [\PhpCsFixer\Tokenizer\CT::T_TYPE_ALTERNATION];
    }
    private function replaceToken(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : void
    {
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_TYPE_ALTERNATION, '|']);
    }
}
