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
namespace PhpCsFixer\Fixer\CastNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class NoUnsetCastFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Variables must be set `null` instead of using `(unset)` casting.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = (unset) \$b;\n")]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_UNSET_CAST);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BinaryOperatorSpacesFixer.
     */
    public function getPriority() : int
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = \count($tokens) - 1; $index > 0; --$index) {
            if ($tokens[$index]->isGivenKind(\T_UNSET_CAST)) {
                $this->fixUnsetCast($tokens, $index);
            }
        }
    }
    private function fixUnsetCast(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : void
    {
        $assignmentIndex = $tokens->getPrevMeaningfulToken($index);
        if (null === $assignmentIndex || !$tokens[$assignmentIndex]->equals('=')) {
            return;
        }
        $varIndex = $tokens->getNextMeaningfulToken($index);
        if (null === $varIndex || !$tokens[$varIndex]->isGivenKind(\T_VARIABLE)) {
            return;
        }
        $afterVar = $tokens->getNextMeaningfulToken($varIndex);
        if (null === $afterVar || !$tokens[$afterVar]->equalsAny([';', [\T_CLOSE_TAG]])) {
            return;
        }
        $nextIsWhiteSpace = $tokens[$assignmentIndex + 1]->isWhitespace();
        $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        $tokens->clearTokenAndMergeSurroundingWhitespace($varIndex);
        ++$assignmentIndex;
        if (!$nextIsWhiteSpace) {
            $tokens->insertAt($assignmentIndex, new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
        }
        ++$assignmentIndex;
        $tokens->insertAt($assignmentIndex, new \PhpCsFixer\Tokenizer\Token([\T_STRING, 'null']));
    }
}
