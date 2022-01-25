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
namespace PhpCsFixer\Fixer\LanguageConstruct;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 */
final class GetClassToClassKeywordFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Replace `get_class` calls on object variables with class keyword syntax.', [new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nget_class(\$a);\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(80000)), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\n\n\$date = new \\DateTimeImmutable();\n\$class = get_class(\$date);\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(80000))], null, 'Risky if the `get_class` function is overridden.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before MultilineWhitespaceBeforeSemicolonsFixer.
     * Must run after NoSpacesAfterFunctionNameFixer, NoSpacesInsideParenthesisFixer.
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
        return \PHP_VERSION_ID >= 80000 && $tokens->isAllTokenKindsFound([\T_STRING, \T_VARIABLE]);
    }
    /**
     * {@inheritdoc}
     */
    public function isRisky() : bool
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $functionsAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        $indicesToClear = [];
        $tokenSlices = [];
        for ($index = $tokens->count() - 1; $index > 0; --$index) {
            if (!$tokens[$index]->equals([\T_STRING, 'get_class'], \false)) {
                continue;
            }
            if (!$functionsAnalyzer->isGlobalFunctionCall($tokens, $index)) {
                continue;
            }
            $braceOpenIndex = $tokens->getNextMeaningfulToken($index);
            $braceCloseIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $braceOpenIndex);
            if ($braceCloseIndex === $tokens->getNextMeaningfulToken($braceOpenIndex)) {
                continue;
                // get_class with no arguments
            }
            $meaningfulTokensCount = 0;
            $variableTokensIndices = [];
            for ($i = $braceOpenIndex + 1; $i < $braceCloseIndex; ++$i) {
                if (!$tokens[$i]->equalsAny([[\T_WHITESPACE], [\T_COMMENT], [\T_DOC_COMMENT], '(', ')'])) {
                    ++$meaningfulTokensCount;
                }
                if (!$tokens[$i]->isGivenKind(\T_VARIABLE)) {
                    continue;
                }
                if ('$this' === \strtolower($tokens[$i]->getContent())) {
                    continue 2;
                    // get_class($this)
                }
                $variableTokensIndices[] = $i;
            }
            if ($meaningfulTokensCount > 1 || 1 !== \count($variableTokensIndices)) {
                continue;
                // argument contains more logic, or more arguments, or no variable argument
            }
            $indicesToClear[$index] = [$braceOpenIndex, \current($variableTokensIndices), $braceCloseIndex];
        }
        foreach ($indicesToClear as $index => $items) {
            $tokenSlices[$index] = $this->getReplacementTokenSlices($tokens, $items[1]);
            $this->clearGetClassCall($tokens, $index, $items[0], $items[2]);
        }
        $tokens->insertSlices($tokenSlices);
    }
    private function getReplacementTokenSlices(\PhpCsFixer\Tokenizer\Tokens $tokens, int $variableIndex) : array
    {
        return [new \PhpCsFixer\Tokenizer\Token([\T_VARIABLE, $tokens[$variableIndex]->getContent()]), new \PhpCsFixer\Tokenizer\Token([\T_DOUBLE_COLON, '::']), new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_CLASS_CONSTANT, 'class'])];
    }
    private function clearGetClassCall(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index, int $braceOpenIndex, int $braceCloseIndex) : void
    {
        for ($i = $braceOpenIndex; $i <= $braceCloseIndex; ++$i) {
            if ($tokens[$i]->isGivenKind([\T_WHITESPACE, \T_COMMENT, \T_DOC_COMMENT])) {
                continue;
            }
            $tokens->clearTokenAndMergeSurroundingWhitespace($i);
        }
        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        if ($tokens[$prevIndex]->isGivenKind(\T_NS_SEPARATOR)) {
            $tokens->clearAt($prevIndex);
        }
        $tokens->clearAt($index);
    }
}
