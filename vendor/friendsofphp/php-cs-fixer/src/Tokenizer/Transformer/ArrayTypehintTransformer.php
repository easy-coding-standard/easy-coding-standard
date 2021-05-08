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
 * Transform `array` typehint from T_ARRAY into CT::T_ARRAY_TYPEHINT.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class ArrayTypehintTransformer extends \PhpCsFixer\Tokenizer\AbstractTransformer
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
        if (!$token->isGivenKind(\T_ARRAY)) {
            return;
        }
        $nextIndex = $tokens->getNextMeaningfulToken($index);
        $nextToken = $tokens[$nextIndex];
        if (!$nextToken->equals('(')) {
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_ARRAY_TYPEHINT, $token->getContent()]);
        }
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function getCustomTokens()
    {
        return [\PhpCsFixer\Tokenizer\CT::T_ARRAY_TYPEHINT];
    }
}
