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
namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class ArrayIndentationFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Each element of an array must be indented exactly once.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$foo = [\n   'bar' => [\n    'baz' => true,\n  ],\n];\n")]);
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound([\T_ARRAY, \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before AlignMultilineCommentFixer, BinaryOperatorSpacesFixer.
     * Must run after BracesFixer, MethodArgumentSpaceFixer, MethodChainingIndentationFixer.
     * @return int
     */
    public function getPriority()
    {
        return 29;
    }
    /**
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $scopes = [];
        $previousLineInitialIndent = '';
        $previousLineNewIndent = '';
        foreach ($tokens as $index => $token) {
            $currentScope = [] !== $scopes ? \count($scopes) - 1 : null;
            if ($token->isComment()) {
                continue;
            }
            if ($token->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN) || $token->equals('(') && $tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind(\T_ARRAY)) {
                $endIndex = $tokens->findBlockEnd($token->equals('(') ? \PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE : \PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_ARRAY_SQUARE_BRACE, $index);
                $scopes[] = ['type' => 'array', 'end_index' => $endIndex, 'initial_indent' => $this->getLineIndentation($tokens, $index)];
                continue;
            }
            if (null === $currentScope) {
                continue;
            }
            if ($token->isWhitespace()) {
                if (!\PhpCsFixer\Preg::match('/\\R/', $token->getContent())) {
                    continue;
                }
                if ('array' === $scopes[$currentScope]['type']) {
                    $indent = \false;
                    for ($searchEndIndex = $index + 1; $searchEndIndex < $scopes[$currentScope]['end_index']; ++$searchEndIndex) {
                        $searchEndToken = $tokens[$searchEndIndex];
                        if (!$searchEndToken->isWhitespace() && !$searchEndToken->isComment() || $searchEndToken->isWhitespace() && \PhpCsFixer\Preg::match('/\\R/', $searchEndToken->getContent())) {
                            $indent = \true;
                            break;
                        }
                    }
                    $content = \PhpCsFixer\Preg::replace('/(\\R+)\\h*$/', '$1' . $scopes[$currentScope]['initial_indent'] . ($indent ? $this->whitespacesConfig->getIndent() : ''), $token->getContent());
                    $previousLineInitialIndent = $this->extractIndent($token->getContent());
                    $previousLineNewIndent = $this->extractIndent($content);
                } else {
                    $content = \PhpCsFixer\Preg::replace('/(\\R)' . \preg_quote($scopes[$currentScope]['initial_indent'], '/') . '(\\h*)$/', '$1' . $scopes[$currentScope]['new_indent'] . '$2', $token->getContent());
                }
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $content]);
                continue;
            }
            if ($index === $scopes[$currentScope]['end_index']) {
                while ([] !== $scopes && $index === $scopes[$currentScope]['end_index']) {
                    \array_pop($scopes);
                    --$currentScope;
                }
                continue;
            }
            if ($token->equals(',')) {
                continue;
            }
            if ('expression' !== $scopes[$currentScope]['type']) {
                $endIndex = $this->findExpressionEndIndex($tokens, $index, $scopes[$currentScope]['end_index']);
                if ($endIndex === $index) {
                    continue;
                }
                $scopes[] = ['type' => 'expression', 'end_index' => $endIndex, 'initial_indent' => $previousLineInitialIndent, 'new_indent' => $previousLineNewIndent];
            }
        }
    }
    /**
     * @param int $index
     * @param int $parentScopeEndIndex
     * @return int
     */
    private function findExpressionEndIndex(\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $parentScopeEndIndex)
    {
        $endIndex = null;
        for ($searchEndIndex = $index + 1; $searchEndIndex < $parentScopeEndIndex; ++$searchEndIndex) {
            $searchEndToken = $tokens[$searchEndIndex];
            if ($searchEndToken->equalsAny(['(', '{']) || $searchEndToken->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN)) {
                $type = \PhpCsFixer\Tokenizer\Tokens::detectBlockType($searchEndToken);
                $searchEndIndex = $tokens->findBlockEnd($type['type'], $searchEndIndex);
                continue;
            }
            if ($searchEndToken->equals(',')) {
                $endIndex = $tokens->getPrevMeaningfulToken($searchEndIndex);
                break;
            }
        }
        if (null !== $endIndex) {
            return $endIndex;
        }
        return $tokens->getPrevMeaningfulToken($parentScopeEndIndex);
    }
    /**
     * @param int $index
     * @return string
     */
    private function getLineIndentation(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $newlineTokenIndex = $this->getPreviousNewlineTokenIndex($tokens, $index);
        if (null === $newlineTokenIndex) {
            return '';
        }
        return $this->extractIndent($this->computeNewLineContent($tokens, $newlineTokenIndex));
    }
    /**
     * @param string $content
     * @return string
     */
    private function extractIndent($content)
    {
        if (\PhpCsFixer\Preg::match('/\\R(\\h*)[^\\r\\n]*$/D', $content, $matches)) {
            return $matches[1];
        }
        return '';
    }
    /**
     * @return int|null
     * @param int $index
     */
    private function getPreviousNewlineTokenIndex(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        while ($index > 0) {
            $index = $tokens->getPrevTokenOfKind($index, [[\T_WHITESPACE], [\T_INLINE_HTML]]);
            if (null === $index) {
                break;
            }
            if ($this->isNewLineToken($tokens, $index)) {
                return $index;
            }
        }
        return null;
    }
    /**
     * @param int $index
     * @return bool
     */
    private function isNewLineToken(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        if (!$tokens[$index]->isGivenKind([\T_WHITESPACE, \T_INLINE_HTML])) {
            return \false;
        }
        return (bool) \PhpCsFixer\Preg::match('/\\R/', $this->computeNewLineContent($tokens, $index));
    }
    /**
     * @param int $index
     * @return string
     */
    private function computeNewLineContent(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $content = $tokens[$index]->getContent();
        if (0 !== $index && $tokens[$index - 1]->equalsAny([[\T_OPEN_TAG], [\T_CLOSE_TAG]])) {
            $content = \PhpCsFixer\Preg::replace('/\\S/', '', $tokens[$index - 1]->getContent()) . $content;
        }
        return $content;
    }
}
