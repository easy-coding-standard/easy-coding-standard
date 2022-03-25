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
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class DateTimeCreateFromFormatCallFixer extends \PhpCsFixer\AbstractFixer
{
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('The first argument of `DateTime::createFromFormat` method must start with `!`.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php \\DateTime::createFromFormat('Y-m-d', '2022-02-11');\n")], "Consider this code:\n    `DateTime::createFromFormat('Y-m-d', '2022-02-11')`.\n    What value will be returned? '2022-01-11 00:00:00.0'? No, actual return value has 'H:i:s' section like '2022-02-11 16:55:37.0'.\n    Change 'Y-m-d' to '!Y-m-d', return value will be '2022-01-11 00:00:00.0'.\n    So, adding `!` to format string will make return value more intuitive.");
    }
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_DOUBLE_COLON);
    }
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $argumentsAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer();
        $namespacesAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer();
        $namespaceUsesAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\NamespaceUsesAnalyzer();
        foreach ($namespacesAnalyzer->getDeclarations($tokens) as $namespace) {
            $scopeStartIndex = $namespace->getScopeStartIndex();
            $useDeclarations = $namespaceUsesAnalyzer->getDeclarationsInNamespace($tokens, $namespace);
            for ($index = $namespace->getScopeEndIndex(); $index > $scopeStartIndex; --$index) {
                if (!$tokens[$index]->isGivenKind(\T_DOUBLE_COLON)) {
                    continue;
                }
                $functionNameIndex = $tokens->getNextMeaningfulToken($index);
                if (!$tokens[$functionNameIndex]->equals([\T_STRING, 'createFromFormat'], \false)) {
                    continue;
                }
                if (!$tokens[$tokens->getNextMeaningfulToken($functionNameIndex)]->equals('(')) {
                    continue;
                }
                $classNameIndex = $tokens->getPrevMeaningfulToken($index);
                if (!$tokens[$classNameIndex]->equals([\T_STRING, 'DateTime'], \false)) {
                    continue;
                }
                $preClassNameIndex = $tokens->getPrevMeaningfulToken($classNameIndex);
                if ($tokens[$preClassNameIndex]->isGivenKind(\T_NS_SEPARATOR)) {
                    if ($tokens[$tokens->getPrevMeaningfulToken($preClassNameIndex)]->isGivenKind(\T_STRING)) {
                        continue;
                    }
                } elseif (!$namespace->isGlobalNamespace()) {
                    continue;
                } else {
                    foreach ($useDeclarations as $useDeclaration) {
                        if ('datetime' === \strtolower($useDeclaration->getShortName()) && 'datetime' !== \strtolower($useDeclaration->getFullName())) {
                            continue 2;
                        }
                    }
                }
                $openIndex = $tokens->getNextTokenOfKind($functionNameIndex, ['(']);
                $closeIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openIndex);
                $argumentIndex = $this->getFirstArgumentTokenIndex($tokens, $argumentsAnalyzer->getArguments($tokens, $openIndex, $closeIndex));
                if (null === $argumentIndex) {
                    continue;
                }
                $format = $tokens[$argumentIndex]->getContent();
                if ('!' === \substr($format, 1, 1)) {
                    continue;
                }
                $tokens->clearAt($argumentIndex);
                $tokens->insertAt($argumentIndex, new \PhpCsFixer\Tokenizer\Token([\T_CONSTANT_ENCAPSED_STRING, \substr_replace($format, '!', 1, 0)]));
            }
        }
    }
    private function getFirstArgumentTokenIndex(\PhpCsFixer\Tokenizer\Tokens $tokens, array $arguments) : ?int
    {
        if (2 !== \count($arguments)) {
            return null;
        }
        \reset($arguments);
        $argumentStartIndex = \key($arguments);
        $argumentEndIndex = $arguments[$argumentStartIndex];
        $argumentStartIndex = $tokens->getNextMeaningfulToken($argumentStartIndex - 1);
        if ($argumentStartIndex !== $argumentEndIndex && $tokens->getNextMeaningfulToken($argumentStartIndex) <= $argumentEndIndex) {
            return null;
            // argument is not a simple single string
        }
        return !$tokens[$argumentStartIndex]->isGivenKind(\T_CONSTANT_ENCAPSED_STRING) ? null : $argumentStartIndex;
    }
}
