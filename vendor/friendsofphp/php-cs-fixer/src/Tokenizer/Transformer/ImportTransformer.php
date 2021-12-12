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
    public function getPriority() : int
    {
        // Should run after CurlyBraceTransformer and ReturnRefTransformer
        return -1;
    }
    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId() : int
    {
        return 50600;
    }
    /**
     * {@inheritdoc}
     */
    public function process(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, int $index) : void
    {
        if (!$token->isGivenKind([\T_CONST, \T_FUNCTION])) {
            return;
        }
        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
        if (!$prevToken->isGivenKind(\T_USE)) {
            $nextToken = $tokens[$tokens->getNextTokenOfKind($index, ['=', '(', [\PhpCsFixer\Tokenizer\CT::T_RETURN_REF], [\PhpCsFixer\Tokenizer\CT::T_GROUP_IMPORT_BRACE_CLOSE]])];
            if (!$nextToken->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_GROUP_IMPORT_BRACE_CLOSE)) {
                return;
            }
        }
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([$token->isGivenKind(\T_FUNCTION) ? \PhpCsFixer\Tokenizer\CT::T_FUNCTION_IMPORT : \PhpCsFixer\Tokenizer\CT::T_CONST_IMPORT, $token->getContent()]);
    }
    /**
     * {@inheritdoc}
     */
    public function getCustomTokens() : array
    {
        return [\PhpCsFixer\Tokenizer\CT::T_CONST_IMPORT, \PhpCsFixer\Tokenizer\CT::T_FUNCTION_IMPORT];
    }
}
