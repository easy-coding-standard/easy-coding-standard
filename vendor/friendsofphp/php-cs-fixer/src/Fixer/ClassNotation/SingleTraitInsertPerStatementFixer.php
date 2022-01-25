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
namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class SingleTraitInsertPerStatementFixer extends \PhpCsFixer\AbstractFixer
{
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
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
     */
    public function getPriority() : int
    {
        return 36;
    }
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT);
    }
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
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
     * @param int[] $candidates ',' indices to fix
     */
    private function fixTraitUse(\PhpCsFixer\Tokenizer\Tokens $tokens, int $useTraitIndex, array $candidates) : void
    {
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
     * @return int[]
     */
    private function getCandidates(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : array
    {
        $indices = [];
        $index = $tokens->getNextTokenOfKind($index, [',', ';', '{']);
        while (!$tokens[$index]->equals(';')) {
            if ($tokens[$index]->equals('{')) {
                return [];
                // do not fix use cases with grouping
            }
            $indices[] = $index;
            $index = $tokens->getNextTokenOfKind($index, [',', ';', '{']);
        }
        return \array_reverse($indices);
    }
}
