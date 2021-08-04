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
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 * @author SpacePossum
 */
final class ProtectedToPrivateFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Converts `protected` variables and methods to `private` where possible.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
final class Sample
{
    protected $a;

    protected function test()
    {
    }
}
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before OrderedClassElementsFixer.
     * Must run after FinalInternalClassFixer.
     */
    public function getPriority() : int
    {
        return 66;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAllTokenKindsFound([\T_CLASS, \T_FINAL, \T_PROTECTED]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $end = \count($tokens) - 3;
        // min. number of tokens to form a class candidate to fix
        for ($index = 0; $index < $end; ++$index) {
            if (!$tokens[$index]->isGivenKind(\T_CLASS)) {
                continue;
            }
            $classOpen = $tokens->getNextTokenOfKind($index, ['{']);
            $classClose = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $classOpen);
            if (!$this->skipClass($tokens, $index, $classOpen, $classClose)) {
                $this->fixClass($tokens, $classOpen, $classClose);
            }
            $index = $classClose;
        }
    }
    private function fixClass(\PhpCsFixer\Tokenizer\Tokens $tokens, int $classOpenIndex, int $classCloseIndex) : void
    {
        for ($index = $classOpenIndex + 1; $index < $classCloseIndex; ++$index) {
            if ($tokens[$index]->equals('{')) {
                $index = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
                continue;
            }
            if (!$tokens[$index]->isGivenKind(\T_PROTECTED)) {
                continue;
            }
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_PRIVATE, 'private']);
        }
    }
    /**
     * Decide whether or not skip the fix for given class.
     */
    private function skipClass(\PhpCsFixer\Tokenizer\Tokens $tokens, int $classIndex, int $classOpenIndex, int $classCloseIndex) : bool
    {
        $prevToken = $tokens[$tokens->getPrevMeaningfulToken($classIndex)];
        if (!$prevToken->isGivenKind(\T_FINAL)) {
            return \true;
        }
        for ($index = $classIndex; $index < $classOpenIndex; ++$index) {
            if ($tokens[$index]->isGivenKind(\T_EXTENDS)) {
                return \true;
            }
        }
        $useIndex = $tokens->getNextTokenOfKind($classIndex, [[\PhpCsFixer\Tokenizer\CT::T_USE_TRAIT]]);
        return $useIndex && $useIndex < $classCloseIndex;
    }
}
