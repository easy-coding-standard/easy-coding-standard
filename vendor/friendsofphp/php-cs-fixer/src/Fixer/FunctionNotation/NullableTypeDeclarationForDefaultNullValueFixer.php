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
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\Analysis\ArgumentAnalysis;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author HypeMC
 */
final class NullableTypeDeclarationForDefaultNullValueFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Adds or removes `?` before type declarations for parameters with a default `null` value.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nfunction sample(string \$str = null)\n{}\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nfunction sample(?string \$str = null)\n{}\n", ['use_nullable_type_declaration' => \false])], 'Rule is applied only in a PHP 7.1+ environment.');
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        if (!$tokens->isTokenKindFound(\T_VARIABLE)) {
            return \false;
        }
        if (\PHP_VERSION_ID >= 70400 && $tokens->isTokenKindFound(\T_FN)) {
            return \true;
        }
        return $tokens->isTokenKindFound(\T_FUNCTION);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoUnreachableDefaultArgumentValueFixer.
     */
    public function getPriority() : int
    {
        return 1;
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('use_nullable_type_declaration', 'Whether to add or remove `?` before type declarations for parameters with a default `null` value.'))->setAllowedTypes(['bool'])->setDefault(\true)->getOption()]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $functionsAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        $tokenKinds = [\T_FUNCTION];
        if (\PHP_VERSION_ID >= 70400) {
            $tokenKinds[] = \T_FN;
        }
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind($tokenKinds)) {
                continue;
            }
            $arguments = $functionsAnalyzer->getFunctionArguments($tokens, $index);
            $this->fixFunctionParameters($tokens, $arguments);
        }
    }
    /**
     * @param ArgumentAnalysis[] $arguments
     */
    private function fixFunctionParameters(\PhpCsFixer\Tokenizer\Tokens $tokens, array $arguments) : void
    {
        $constructorPropertyModifiers = [\PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PUBLIC, \PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PROTECTED, \PhpCsFixer\Tokenizer\CT::T_CONSTRUCTOR_PROPERTY_PROMOTION_PRIVATE];
        if (\defined('T_READONLY')) {
            // @TODO: drop condition when PHP 8.1+ is required
            $constructorPropertyModifiers[] = T_READONLY;
        }
        foreach (\array_reverse($arguments) as $argumentInfo) {
            if (!$argumentInfo->hasTypeAnalysis() || \strpos($argumentInfo->getTypeAnalysis()->getName(), '|') !== \false || !$argumentInfo->hasDefault() || 'null' !== \strtolower($argumentInfo->getDefault())) {
                continue;
            }
            $argumentTypeInfo = $argumentInfo->getTypeAnalysis();
            if (\PHP_VERSION_ID >= 80000 && \false === $this->configuration['use_nullable_type_declaration']) {
                $visibility = $tokens[$tokens->getPrevMeaningfulToken($argumentTypeInfo->getStartIndex())];
                if ($visibility->isGivenKind($constructorPropertyModifiers)) {
                    continue;
                }
            }
            if (\true === $this->configuration['use_nullable_type_declaration']) {
                if (!$argumentTypeInfo->isNullable() && 'mixed' !== $argumentTypeInfo->getName()) {
                    $tokens->insertAt($argumentTypeInfo->getStartIndex(), new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_NULLABLE_TYPE, '?']));
                }
            } else {
                if ($argumentTypeInfo->isNullable()) {
                    $tokens->removeTrailingWhitespace($argumentTypeInfo->getStartIndex());
                    $tokens->clearTokenAndMergeSurroundingWhitespace($argumentTypeInfo->getStartIndex());
                }
            }
        }
    }
}
