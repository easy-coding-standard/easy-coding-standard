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
namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer;
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitStrictFixer extends \PhpCsFixer\Fixer\AbstractPhpUnitFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * @var array<string,string>
     */
    private static $assertionMap = ['assertAttributeEquals' => 'assertAttributeSame', 'assertAttributeNotEquals' => 'assertAttributeNotSame', 'assertEquals' => 'assertSame', 'assertNotEquals' => 'assertNotSame'];
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('PHPUnit methods like `assertSame` should be used instead of `assertEquals`.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testSomeTest()
    {
        $this->assertAttributeEquals(a(), b());
        $this->assertAttributeNotEquals(a(), b());
        $this->assertEquals(a(), b());
        $this->assertNotEquals(a(), b());
    }
}
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    public function testSomeTest()
    {
        $this->assertAttributeEquals(a(), b());
        $this->assertAttributeNotEquals(a(), b());
        $this->assertEquals(a(), b());
        $this->assertNotEquals(a(), b());
    }
}
', ['assertions' => ['assertEquals']])], null, 'Risky when any of the functions are overridden or when testing object equality.');
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
    protected function applyPhpUnitClassFix(\PhpCsFixer\Tokenizer\Tokens $tokens, int $startIndex, int $endIndex) : void
    {
        $argumentsAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\ArgumentsAnalyzer();
        $functionsAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        foreach ($this->configuration['assertions'] as $methodBefore) {
            $methodAfter = self::$assertionMap[$methodBefore];
            for ($index = $startIndex; $index < $endIndex; ++$index) {
                $methodIndex = $tokens->getNextTokenOfKind($index, [[\T_STRING, $methodBefore]]);
                if (null === $methodIndex) {
                    break;
                }
                if (!$functionsAnalyzer->isTheSameClassCall($tokens, $methodIndex)) {
                    continue;
                }
                $openingParenthesisIndex = $tokens->getNextMeaningfulToken($methodIndex);
                $argumentsCount = $argumentsAnalyzer->countArguments($tokens, $openingParenthesisIndex, $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $openingParenthesisIndex));
                if (2 === $argumentsCount || 3 === $argumentsCount) {
                    $tokens[$methodIndex] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, $methodAfter]);
                }
                $index = $methodIndex;
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('assertions', 'List of assertion methods to fix.'))->setAllowedTypes(['array'])->setAllowedValues([new \PhpCsFixer\FixerConfiguration\AllowedValueSubset(\array_keys(self::$assertionMap))])->setDefault(['assertAttributeEquals', 'assertAttributeNotEquals', 'assertEquals', 'assertNotEquals'])->getOption()]);
    }
}
