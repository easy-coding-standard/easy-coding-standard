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
namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 */
final class SingleTraitInsertPerStatementFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Each trait `use` must be done as single statement.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
final class Example
{
    use Foo, Bar;
}
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before BracesFixer, SpaceAfterSemicolonFixer.
     * @return int
     */
    public function getPriority()
    {
        return 36;
    }
    /**
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT);
    }
    /**
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        for ($index = \count($tokens) - 1; 1 < $index; --$index) {
            if ($tokens[$index]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT)) {
                $candidates = $this->getCandidates($tokens, $index);
                if (\count($candidates) > 0) {
                    $this->fixTraitUse($tokens, $index, $candidates);
                }
            }
        }
    }
    /**
     * @param int[] $candidates ',' indexes to fix
     * @return void
     * @param int $useTraitIndex
     */
    private function fixTraitUse(\PhpCsFixer\Tokenizer\Tokens $tokens, $useTraitIndex, array $candidates)
    {
        $useTraitIndex = (int) $useTraitIndex;
        foreach ($candidates as $commaIndex) {
            $inserts = [new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT, 'use']), new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])];
            $nextImportStartIndex = $tokens->getNextMeaningfulToken($commaIndex);
            if ($tokens[$nextImportStartIndex - 1]->isWhitespace()) {
                if (1 === \PhpCsFixer\Preg::match('/\\R/', $tokens[$nextImportStartIndex - 1]->getContent())) {
                    \array_unshift($inserts, clone $tokens[$useTraitIndex - 1]);
                }
                $tokens->clearAt($nextImportStartIndex - 1);
            }
            $tokens[$commaIndex] = new \PhpCsFixer\Tokenizer\Token(';');
            $tokens->insertAt($nextImportStartIndex, $inserts);
        }
    }
    /**
     * @return mixed[]
     * @param int $index
     */
    private function getCandidates(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $index = (int) $index;
        $indexes = [];
        $index = $tokens->getNextTokenOfKind($index, [',', ';', '{']);
        while (!$tokens[$index]->equals(';')) {
            if ($tokens[$index]->equals('{')) {
                return [];
                // do not fix use cases with grouping
            }
            $indexes[] = $index;
            $index = $tokens->getNextTokenOfKind($index, [',', ';', '{']);
        }
        return \array_reverse($indexes);
    }
}
