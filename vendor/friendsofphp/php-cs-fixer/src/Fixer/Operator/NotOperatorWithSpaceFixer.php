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
 * @author Javier Spagnoletti <phansys@gmail.com>
 */
final class NotOperatorWithSpaceFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Logical NOT operators (`!`) should have leading and trailing whitespaces.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php

if (!$bar) {
    echo "Help!";
}
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after ModernizeStrposFixer, UnaryOperatorSpacesFixer.
     */
    public function getPriority() : int
    {
        return -10;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound('!');
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];
            if ($token->equals('!')) {
                if (!$tokens[$index + 1]->isWhitespace()) {
                    $tokens->insertAt($index + 1, new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
                }
                if (!$tokens[$index - 1]->isWhitespace()) {
                    $tokens->insertAt($index, new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
                }
            }
        }
    }
}
