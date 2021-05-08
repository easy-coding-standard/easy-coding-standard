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
namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Kuba Werłos <werlos@gmail.com>
 */
final class ImplodeCallFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Function `implode` must be called with 2 arguments in the documented order.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nimplode(\$pieces, '');\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nimplode(\$pieces);\n")], null, 'Risky when the function `implode` is overridden.');
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_STRING);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before MethodArgumentSpaceFixer.
     * Must run after NoAliasFunctionsFixer.
     * @return int
     */
    public function getPriority()
    {
        return 37;
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $functionsAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        for ($index = \count($tokens) - 1; $index > 0; --$index) {
            if (!$tokens[$index]->equals([\T_STRING, 'implode'], \false)) {
                continue;
            }
            if (!$functionsAnalyzer->isGlobalFunctionCall($tokens, $index)) {
                continue;
            }
            $argumentsIndices = $this->getArgumentIndices($tokens, $index);
            if (1 === \count($argumentsIndices)) {
                $firstArgumentIndex = \key($argumentsIndices);
                $tokens->insertAt($firstArgumentIndex, [new \PhpCsFixer\Tokenizer\Token([\T_CONSTANT_ENCAPSED_STRING, "''"]), new \PhpCsFixer\Tokenizer\Token(','), new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])]);
                continue;
            }
            if (2 === \count($argumentsIndices)) {
                list($firstArgumentIndex, $secondArgumentIndex) = \array_keys($argumentsIndices);
                // If the first argument is string we have nothing to do
                if ($tokens[$firstArgumentIndex]->isGivenKind(\T_CONSTANT_ENCAPSED_STRING)) {
                    continue;
                }
                // If the second argument is not string we cannot make a swap
                if (!$tokens[$secondArgumentIndex]->isGivenKind(\T_CONSTANT_ENCAPSED_STRING)) {
                    continue;
                }
                // collect tokens from first argument
                $firstArgumentEndIndex = $argumentsIndices[\key($argumentsIndices)];
                $newSecondArgumentTokens = [];
                for ($i = \key($argumentsIndices); $i <= $firstArgumentEndIndex; ++$i) {
                    $newSecondArgumentTokens[] = clone $tokens[$i];
                    $tokens->clearAt($i);
                }
                $tokens->insertAt($firstArgumentIndex, clone $tokens[$secondArgumentIndex]);
                // insert above increased the second argument index
                ++$secondArgumentIndex;
                $tokens->clearAt($secondArgumentIndex);
                $tokens->insertAt($secondArgumentIndex, $newSecondArgumentTokens);
            }
        }
    }
    /**
     * @return mixed[] In the format: startIndex => endIndex
     * @param int $functionNameIndex
     */
    private function getArgumentIndices(\PhpCsFixer\Tokenizer\Tokens $tokens, $functionNameIndex)
    {
        $argumentsAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer();
        $openParenthesis = $tokens->getNextTokenOfKind($functionNameIndex, ['(']);
        $closeParenthesis = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openParenthesis);
        $indices = [];
        foreach ($argumentsAnalyzer->getArguments($tokens, $openParenthesis, $closeParenthesis) as $startIndexCandidate => $endIndex) {
            $indices[$tokens->getNextMeaningfulToken($startIndexCandidate - 1)] = $tokens->getPrevMeaningfulToken($endIndex + 1);
        }
        return $indices;
    }
}
