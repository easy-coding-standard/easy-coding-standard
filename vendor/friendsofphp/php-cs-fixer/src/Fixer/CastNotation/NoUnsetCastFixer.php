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
namespace PhpCsFixer\Fixer\CastNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 */
final class NoUnsetCastFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Variables must be set `null` instead of using `(unset)` casting.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = (unset) \$b;\n")]);
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    public function isCandidate($tokens)
    {
        return $tokens->isTokenKindFound(\T_UNSET_CAST);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BinaryOperatorSpacesFixer.
     * @return int
     */
    public function getPriority()
    {
        return 0;
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param \SplFileInfo $file
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    protected function applyFix($file, $tokens)
    {
        for ($index = \count($tokens) - 1; $index > 0; --$index) {
            if ($tokens[$index]->isGivenKind(\T_UNSET_CAST)) {
                $this->fixUnsetCast($tokens, $index);
            }
        }
    }
    /**
     * @return void
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     */
    private function fixUnsetCast($tokens, $index)
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
