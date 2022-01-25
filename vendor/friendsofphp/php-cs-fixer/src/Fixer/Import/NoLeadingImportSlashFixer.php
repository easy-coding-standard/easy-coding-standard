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
namespace PhpCsFixer\Fixer\Import;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 */
final class NoLeadingImportSlashFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Remove leading slashes in `use` clauses.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nnamespace Foo;\nuse \\Bar;\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before OrderedImportsFixer.
     * Must run after NoUnusedImportsFixer, SingleImportPerStatementFixer.
     */
    public function getPriority() : int
    {
        return -20;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_USE);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        $usesIndices = $tokensAnalyzer->getImportUseIndexes();
        foreach ($usesIndices as $idx) {
            $nextTokenIdx = $tokens->getNextMeaningfulToken($idx);
            $nextToken = $tokens[$nextTokenIdx];
            if ($nextToken->isGivenKind(\T_NS_SEPARATOR)) {
                $this->removeLeadingImportSlash($tokens, $nextTokenIdx);
            } elseif ($nextToken->isGivenKind([\PhpCsFixer\Tokenizer\CT::T_FUNCTION_IMPORT, \PhpCsFixer\Tokenizer\CT::T_CONST_IMPORT])) {
                $nextTokenIdx = $tokens->getNextMeaningfulToken($nextTokenIdx);
                if ($tokens[$nextTokenIdx]->isGivenKind(\T_NS_SEPARATOR)) {
                    $this->removeLeadingImportSlash($tokens, $nextTokenIdx);
                }
            }
        }
    }
    private function removeLeadingImportSlash(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : void
    {
        $previousIndex = $tokens->getPrevNonWhitespace($index);
        if ($previousIndex < $index - 1 || $tokens[$previousIndex]->isComment()) {
            $tokens->clearAt($index);
            return;
        }
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
    }
}
