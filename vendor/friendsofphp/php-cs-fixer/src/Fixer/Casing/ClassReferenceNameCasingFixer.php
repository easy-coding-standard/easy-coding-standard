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
namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class ClassReferenceNameCasingFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('When referencing a class it must be written using the correct casing.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nthrow new \\exception();\n")]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_STRING);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $namespacesAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\NamespacesAnalyzer();
        $classNames = $this->getClassNames();
        foreach ($namespacesAnalyzer->getDeclarations($tokens) as $namespace) {
            foreach ($this->getClassReference($tokens, $namespace) as $reference) {
                $currentContent = $tokens[$reference]->getContent();
                $lowerCurrentContent = \strtolower($currentContent);
                if (isset($classNames[$lowerCurrentContent]) && $currentContent !== $classNames[$lowerCurrentContent]) {
                    $tokens[$reference] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, $classNames[$lowerCurrentContent]]);
                }
            }
        }
    }
    private function getClassReference(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Analyzer\Analysis\NamespaceAnalysis $namespace) : \Generator
    {
        static $notBeforeKinds;
        if (null === $notBeforeKinds) {
            $notBeforeKinds = [
                \PhpCsFixer\Tokenizer\CT::T_USE_TRAIT,
                \T_AS,
                \T_CASE,
                // PHP 8.1 trait enum-case
                \T_CLASS,
                \T_CONST,
                \T_DOUBLE_COLON,
                \T_FUNCTION,
                \T_INTERFACE,
                \T_OBJECT_OPERATOR,
                \T_TRAIT,
            ];
            if (\defined('T_ENUM')) {
                // @TODO: drop condition when PHP 8.1+ is required
                $notBeforeKinds[] = T_ENUM;
            }
        }
        $namespaceIsGlobal = $namespace->isGlobalNamespace();
        for ($index = $namespace->getScopeStartIndex(); $index < $namespace->getScopeEndIndex(); ++$index) {
            if (!$tokens[$index]->isGivenKind(\T_STRING)) {
                continue;
            }
            $nextIndex = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$nextIndex]->isGivenKind(\T_NS_SEPARATOR)) {
                continue;
            }
            $prevIndex = $tokens->getPrevMeaningfulToken($index);
            $isNamespaceSeparator = $tokens[$prevIndex]->isGivenKind(\T_NS_SEPARATOR);
            if (!$isNamespaceSeparator && !$namespaceIsGlobal) {
                continue;
            }
            if ($isNamespaceSeparator) {
                $prevIndex = $tokens->getPrevMeaningfulToken($prevIndex);
                if ($tokens[$prevIndex]->isGivenKind(\T_STRING)) {
                    continue;
                }
            } elseif ($tokens[$prevIndex]->isGivenKind($notBeforeKinds)) {
                continue;
            }
            if (!$tokens[$prevIndex]->isGivenKind([\T_NEW]) && $tokens[$nextIndex]->equals('(')) {
                continue;
            }
            (yield $index);
        }
    }
    private function getClassNames() : array
    {
        static $classes = null;
        if (null === $classes) {
            $classes = [];
            foreach (\get_declared_classes() as $class) {
                if ((new \ReflectionClass($class))->isInternal()) {
                    $classes[\strtolower($class)] = $class;
                }
            }
        }
        return $classes;
    }
}
