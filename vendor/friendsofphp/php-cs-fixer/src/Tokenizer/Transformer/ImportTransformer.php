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
 * Transform const/function import tokens.
 *
 * Performed transformations:
 * - T_CONST into CT::T_CONST_IMPORT
 * - T_FUNCTION into CT::T_FUNCTION_IMPORT
 *
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 */
final class ImportTransformer extends \PhpCsFixer\Tokenizer\AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId() : int
    {
        return 50600;
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param \PhpCsFixer\Tokenizer\Token $token
     * @param int $index
     */
    public function process($tokens, $token, $index) : void
    {
        if (!$token->isGivenKind([\T_CONST, \T_FUNCTION])) {
            return;
        }
        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
        if ($prevToken->isGivenKind(\T_USE)) {
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([$token->isGivenKind(\T_FUNCTION) ? \PhpCsFixer\Tokenizer\CT::T_FUNCTION_IMPORT : \PhpCsFixer\Tokenizer\CT::T_CONST_IMPORT, $token->getContent()]);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getCustomTokens() : array
    {
        return [\PhpCsFixer\Tokenizer\CT::T_CONST_IMPORT, \PhpCsFixer\Tokenizer\CT::T_FUNCTION_IMPORT];
    }
}
