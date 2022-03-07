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
namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\AttributeAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
final class NoTrailingCommaInSinglelineFunctionCallFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('When making a method or function call on a single line there MUST NOT be a trailing comma after the last argument.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nfoo(\$a,);\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoSpacesInsideParenthesisFixer.
     */
    public function getPriority() : int
    {
        return 3;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound([\T_STRING, \T_VARIABLE, \T_CLASS, \T_UNSET, \T_ISSET, \T_LIST]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = \count($tokens) - 1; $index > 0; --$index) {
            if (!$tokens[$index]->equals(')')) {
                continue;
            }
            $trailingCommaIndex = $tokens->getPrevMeaningfulToken($index);
            if (!$tokens[$trailingCommaIndex]->equals(',')) {
                continue;
            }
            $callIndex = $tokens->getPrevMeaningfulToken(
                // get before "parenthesis open index"
                $tokens->findBlockStart(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $index)
            );
            if ($tokens[$callIndex]->isGivenKind([\T_VARIABLE, \T_CLASS, \T_UNSET, \T_ISSET, \T_LIST])) {
                $this->clearCommaIfNeeded($tokens, $callIndex, $index, $trailingCommaIndex);
                continue;
            }
            if ($tokens[$callIndex]->isGivenKind(\T_STRING)) {
                if (!\PhpCsFixer\Tokenizer\Analyzer\AttributeAnalyzer::isAttribute($tokens, $callIndex)) {
                    $this->clearCommaIfNeeded($tokens, $callIndex, $index, $trailingCommaIndex);
                }
                continue;
            }
            if ($tokens[$callIndex]->equalsAny([')', ']', [\PhpCsFixer\Tokenizer\CT::T_DYNAMIC_VAR_BRACE_CLOSE], [\PhpCsFixer\Tokenizer\CT::T_ARRAY_INDEX_CURLY_BRACE_CLOSE]])) {
                $block = \PhpCsFixer\Tokenizer\Tokens::detectBlockType($tokens[$callIndex]);
                if (\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_ARRAY_INDEX_CURLY_BRACE === $block['type'] || \PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_DYNAMIC_VAR_BRACE === $block['type'] || \PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE === $block['type'] || \PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE === $block['type']) {
                    $this->clearCommaIfNeeded($tokens, $callIndex, $index, $trailingCommaIndex);
                    // continue; implicit
                }
            }
        }
    }
    private function clearCommaIfNeeded(\PhpCsFixer\Tokenizer\Tokens $tokens, int $startIndex, int $endIndex, int $commaIndex) : void
    {
        if (!$tokens->isPartialCodeMultiline($startIndex, $endIndex)) {
            $tokens->clearTokenAndMergeSurroundingWhitespace($commaIndex);
        }
    }
}
