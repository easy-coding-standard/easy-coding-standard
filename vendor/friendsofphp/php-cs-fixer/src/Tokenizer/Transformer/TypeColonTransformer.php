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
 * Transform `:` operator into CT::T_TYPE_COLON in `function foo() : int {}`.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class TypeColonTransformer extends \PhpCsFixer\Tokenizer\AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getPriority() : int
    {
        // needs to run after ReturnRefTransformer and UseTransformer
        // and before TypeAlternationTransformer
        return -10;
    }
    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId() : int
    {
        return 70000;
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param \PhpCsFixer\Tokenizer\Token $token
     * @param int $index
     */
    public function process($tokens, $token, $index) : void
    {
        if (!$token->equals(':')) {
            return;
        }
        $endIndex = $tokens->getPrevMeaningfulToken($index);
        if (!$tokens[$endIndex]->equals(')')) {
            return;
        }
        $startIndex = $tokens->findBlockStart(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $endIndex);
        $prevIndex = $tokens->getPrevMeaningfulToken($startIndex);
        $prevToken = $tokens[$prevIndex];
        // if this could be a function name we need to take one more step
        if ($prevToken->isGivenKind(\T_STRING)) {
            $prevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
            $prevToken = $tokens[$prevIndex];
        }
        $prevKinds = [\T_FUNCTION, \PhpCsFixer\Tokenizer\CT::T_RETURN_REF, \PhpCsFixer\Tokenizer\CT::T_USE_LAMBDA];
        if (\PHP_VERSION_ID >= 70400) {
            $prevKinds[] = \T_FN;
        }
        if ($prevToken->isGivenKind($prevKinds)) {
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON, ':']);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getCustomTokens() : array
    {
        return [\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON];
    }
}
