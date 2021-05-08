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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * Fixer for rules defined in PSR2 ¶3.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 * @author SpacePossum
 */
final class SingleImportPerStatementFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('There MUST be one use keyword per declaration.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nuse Foo, Sample, Sample\\Sample as Sample2;\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before MultilineWhitespaceBeforeSemicolonsFixer, NoLeadingImportSlashFixer, NoSinglelineWhitespaceBeforeSemicolonsFixer, NoUnusedImportsFixer, SpaceAfterSemicolonFixer.
     * @return int
     */
    public function getPriority()
    {
        return 1;
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
        $uses = \array_reverse($tokensAnalyzer->getImportUseIndexes());
        foreach ($uses as $index) {
            $endIndex = $tokens->getNextTokenOfKind($index, [';', [\T_CLOSE_TAG]]);
            $groupClose = $tokens->getPrevMeaningfulToken($endIndex);
            if ($tokens[$groupClose]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_GROUP_IMPORT_BRACE_CLOSE)) {
                $this->fixGroupUse($tokens, $index, $endIndex);
            } else {
                $this->fixMultipleUse($tokens, $index, $endIndex);
            }
        }
    }
    /**
     * @param int $index
     * @return mixed[]
     */
    private function getGroupDeclaration(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $groupPrefix = '';
        $comment = '';
        $groupOpenIndex = null;
        for ($i = $index + 1;; ++$i) {
            if ($tokens[$i]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_GROUP_IMPORT_BRACE_OPEN)) {
                $groupOpenIndex = $i;
                break;
            }
            if ($tokens[$i]->isComment()) {
                $comment .= $tokens[$i]->getContent();
                if (!$tokens[$i - 1]->isWhitespace() && !$tokens[$i + 1]->isWhitespace()) {
                    $groupPrefix .= ' ';
                }
                continue;
            }
            if ($tokens[$i]->isWhitespace()) {
                $groupPrefix .= ' ';
                continue;
            }
            $groupPrefix .= $tokens[$i]->getContent();
        }
        return [\rtrim($groupPrefix), $groupOpenIndex, $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_GROUP_IMPORT_BRACE, $groupOpenIndex), $comment];
    }
    /**
     * @return mixed[]
     * @param string $groupPrefix
     * @param int $groupOpenIndex
     * @param int $groupCloseIndex
     * @param string $comment
     */
    private function getGroupStatements(\PhpCsFixer\Tokenizer\Tokens $tokens, $groupPrefix, $groupOpenIndex, $groupCloseIndex, $comment)
    {
        $statements = [];
        $statement = $groupPrefix;
        for ($i = $groupOpenIndex + 1; $i <= $groupCloseIndex; ++$i) {
            $token = $tokens[$i];
            if ($token->equals(',') && $tokens[$tokens->getNextMeaningfulToken($i)]->equals([\PhpCsFixer\Tokenizer\CT::T_GROUP_IMPORT_BRACE_CLOSE])) {
                continue;
            }
            if ($token->equalsAny([',', [\PhpCsFixer\Tokenizer\CT::T_GROUP_IMPORT_BRACE_CLOSE]])) {
                $statements[] = 'use' . $statement . ';';
                $statement = $groupPrefix;
                continue;
            }
            if ($token->isWhitespace()) {
                $j = $tokens->getNextMeaningfulToken($i);
                if ($tokens[$j]->equals([\T_AS])) {
                    $statement .= ' as ';
                    $i += 2;
                } elseif ($tokens[$j]->equals([\T_FUNCTION])) {
                    $statement = ' function' . $statement;
                    $i += 2;
                } elseif ($tokens[$j]->equals([\T_CONST])) {
                    $statement = ' const' . $statement;
                    $i += 2;
                }
                if ($token->isWhitespace(" \t") || '//' !== \substr($tokens[$i - 1]->getContent(), 0, 2)) {
                    continue;
                }
            }
            $statement .= $token->getContent();
        }
        if ('' !== $comment) {
            $statements[0] .= ' ' . $comment;
        }
        return $statements;
    }
    /**
     * @return void
     * @param int $index
     * @param int $endIndex
     */
    private function fixGroupUse(\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $endIndex)
    {
        list($groupPrefix, $groupOpenIndex, $groupCloseIndex, $comment) = $this->getGroupDeclaration($tokens, $index);
        $statements = $this->getGroupStatements($tokens, $groupPrefix, $groupOpenIndex, $groupCloseIndex, $comment);
        if (\count($statements) < 2) {
            return;
        }
        $tokens->clearRange($index, $groupCloseIndex);
        if ($tokens[$endIndex]->equals(';')) {
            $tokens->clearAt($endIndex);
        }
        $ending = $this->whitespacesConfig->getLineEnding();
        $importTokens = \PhpCsFixer\Tokenizer\Tokens::fromCode('<?php ' . \implode($ending, $statements));
        $importTokens->clearAt(0);
        $importTokens->clearEmptyTokens();
        $tokens->insertAt($index, $importTokens);
    }
    /**
     * @return void
     * @param int $index
     * @param int $endIndex
     */
    private function fixMultipleUse(\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $endIndex)
    {
        $ending = $this->whitespacesConfig->getLineEnding();
        for ($i = $endIndex - 1; $i > $index; --$i) {
            if (!$tokens[$i]->equals(',')) {
                continue;
            }
            $tokens[$i] = new \PhpCsFixer\Tokenizer\Token(';');
            $i = $tokens->getNextMeaningfulToken($i);
            $tokens->insertAt($i, new \PhpCsFixer\Tokenizer\Token([\T_USE, 'use']));
            $tokens->insertAt($i + 1, new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
            $indent = \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $index);
            if ($tokens[$i - 1]->isWhitespace()) {
                $tokens[$i - 1] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $ending . $indent]);
                continue;
            }
            if (\false === \strpos($tokens[$i - 1]->getContent(), "\n")) {
                $tokens->insertAt($i, new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $ending . $indent]));
            }
        }
    }
}
