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
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Remove leading slashes in `use` clauses.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nnamespace Foo;\nuse \\Bar;\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before OrderedImportsFixer.
     * Must run after NoUnusedImportsFixer, SingleImportPerStatementFixer.
     * @return int
     */
    public function getPriority()
    {
        return -20;
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_USE);
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        $usesIndexes = $tokensAnalyzer->getImportUseIndexes();
        foreach ($usesIndexes as $idx) {
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
    /**
     * @return void
     * @param int $index
     */
    private function removeLeadingImportSlash(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $previousIndex = $tokens->getPrevNonWhitespace($index);
        if ($previousIndex < $index - 1 || $tokens[$previousIndex]->isComment()) {
            $tokens->clearAt($index);
            return;
        }
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']);
    }
}
