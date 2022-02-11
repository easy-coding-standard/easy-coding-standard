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
 */
final class SingleImportPerStatementFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('There MUST be one use keyword per declaration.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nuse Foo, Sample, Sample\\Sample as Sample2;\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before MultilineWhitespaceBeforeSemicolonsFixer, NoLeadingImportSlashFixer, NoSinglelineWhitespaceBeforeSemicolonsFixer, NoUnusedImportsFixer, SpaceAfterSemicolonFixer.
     */
    public function getPriority() : int
    {
        return 1;
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
        foreach (\array_reverse($tokensAnalyzer->getImportUseIndexes()) as $index) {
            $endIndex = $tokens->getNextTokenOfKind($index, [';', [\T_CLOSE_TAG]]);
            $groupClose = $tokens->getPrevMeaningfulToken($endIndex);
            if ($tokens[$groupClose]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_GROUP_IMPORT_BRACE_CLOSE)) {
                $this->fixGroupUse($tokens, $index, $endIndex);
            } else {
                $this->fixMultipleUse($tokens, $index, $endIndex);
            }
        }
    }
    private function getGroupDeclaration(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : array
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
     * @return string[]
     */
    private function getGroupStatements(\PhpCsFixer\Tokenizer\Tokens $tokens, string $groupPrefix, int $groupOpenIndex, int $groupCloseIndex, string $comment) : array
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
                if ($tokens[$j]->isGivenKind(\T_AS)) {
                    $statement .= ' as ';
                    $i += 2;
                } elseif ($tokens[$j]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_FUNCTION_IMPORT)) {
                    $statement = ' function' . $statement;
                    $i += 2;
                } elseif ($tokens[$j]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_CONST_IMPORT)) {
                    $statement = ' const' . $statement;
                    $i += 2;
                }
                if ($token->isWhitespace(" \t") || \strncmp($tokens[$i - 1]->getContent(), '//', \strlen('//')) !== 0) {
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
    private function fixGroupUse(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index, int $endIndex) : void
    {
        [$groupPrefix, $groupOpenIndex, $groupCloseIndex, $comment] = $this->getGroupDeclaration($tokens, $index);
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
    private function fixMultipleUse(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index, int $endIndex) : void
    {
        $nextTokenIndex = $tokens->getNextMeaningfulToken($index);
        if ($tokens[$nextTokenIndex]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_FUNCTION_IMPORT)) {
            $leadingTokens = [new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_FUNCTION_IMPORT, 'function']), new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])];
        } elseif ($tokens[$nextTokenIndex]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_CONST_IMPORT)) {
            $leadingTokens = [new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_CONST_IMPORT, 'const']), new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])];
        } else {
            $leadingTokens = [];
        }
        $ending = $this->whitespacesConfig->getLineEnding();
        for ($i = $endIndex - 1; $i > $index; --$i) {
            if (!$tokens[$i]->equals(',')) {
                continue;
            }
            $tokens[$i] = new \PhpCsFixer\Tokenizer\Token(';');
            $i = $tokens->getNextMeaningfulToken($i);
            $tokens->insertAt($i, new \PhpCsFixer\Tokenizer\Token([\T_USE, 'use']));
            $tokens->insertAt($i + 1, new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']));
            foreach ($leadingTokens as $offset => $leadingToken) {
                $tokens->insertAt($i + 2 + $offset, clone $leadingTokens[$offset]);
            }
            $indent = \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $index);
            if ($tokens[$i - 1]->isWhitespace()) {
                $tokens[$i - 1] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $ending . $indent]);
            } elseif (\strpos($tokens[$i - 1]->getContent(), "\n") === \false) {
                $tokens->insertAt($i, new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $ending . $indent]));
            }
        }
    }
}
