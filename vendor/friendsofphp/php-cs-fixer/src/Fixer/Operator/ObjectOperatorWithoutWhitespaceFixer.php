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
namespace PhpCsFixer\Fixer\Operator;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class ObjectOperatorWithoutWhitespaceFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('There should not be space before or after object operators `->` and `?->`.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php \$a  ->  b;\n")]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound(\PhpCsFixer\Tokenizer\Token::getObjectOperatorKinds());
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        // [Structure] there should not be space before or after "->" or "?->"
        foreach ($tokens as $index => $token) {
            if (!$token->isObjectOperator()) {
                continue;
            }
            // clear whitespace before ->
            if ($tokens[$index - 1]->isWhitespace(" \t") && !$tokens[$index - 2]->isComment()) {
                $tokens->clearAt($index - 1);
            }
            // clear whitespace after ->
            if ($tokens[$index + 1]->isWhitespace(" \t") && !$tokens[$index + 2]->isComment()) {
                $tokens->clearAt($index + 1);
            }
        }
    }
}
