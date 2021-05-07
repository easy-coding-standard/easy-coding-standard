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
use PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpUnitConstructFixer extends \PhpCsFixer\Fixer\AbstractPhpUnitFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    private static $assertionFixers = ['assertSame' => 'fixAssertPositive', 'assertEquals' => 'fixAssertPositive', 'assertNotEquals' => 'fixAssertNegative', 'assertNotSame' => 'fixAssertNegative'];
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
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('PHPUnit assertion method calls like `->assertSame(true, $foo)` should be written with dedicated method like `->assertTrue($foo)`.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
final class FooTest extends \\PHPUnit_Framework_TestCase {
    public function testSomething() {
        $this->assertEquals(false, $b);
        $this->assertSame(true, $a);
        $this->assertNotEquals(null, $c);
        $this->assertNotSame(null, $d);
    }
}
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
final class FooTest extends \\PHPUnit_Framework_TestCase {
    public function testSomething() {
        $this->assertEquals(false, $b);
        $this->assertSame(true, $a);
        $this->assertNotEquals(null, $c);
        $this->assertNotSame(null, $d);
    }
}
', ['assertions' => ['assertSame', 'assertNotSame']])], null, 'Fixer could be risky if one is overriding PHPUnit\'s native methods.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpUnitDedicateAssertFixer.
     * @return int
     */
    public function getPriority()
    {
        return -10;
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $startIndex
     * @param int $endIndex
     */
    protected function applyPhpUnitClassFix($tokens, $startIndex, $endIndex)
    {
        // no assertions to be fixed - fast return
        if (empty($this->configuration['assertions'])) {
            return;
        }
        foreach ($this->configuration['assertions'] as $assertionMethod) {
            $assertionFixer = self::$assertionFixers[$assertionMethod];
            for ($index = $startIndex; $index < $endIndex; ++$index) {
                $index = $this->{$assertionFixer}($tokens, $index, $assertionMethod);
                if (null === $index) {
                    break;
                }
            }
        }
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
     */
    protected function createConfigurationDefinition()
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('assertions', 'List of assertion methods to fix.'))->setAllowedTypes(['array'])->setAllowedValues([new \PhpCsFixer\FixerConfiguration\AllowedValueSubset(\array_keys(self::$assertionFixers))])->setDefault(['assertEquals', 'assertSame', 'assertNotEquals', 'assertNotSame'])->getOption()]);
    }
    /**
     * @return int|null
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @param string $method
     */
    private function fixAssertNegative($tokens, $index, $method)
    {
        static $map = ['false' => 'assertNotFalse', 'null' => 'assertNotNull', 'true' => 'assertNotTrue'];
        return $this->fixAssert($map, $tokens, $index, $method);
    }
    /**
     * @return int|null
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @param string $method
     */
    private function fixAssertPositive($tokens, $index, $method)
    {
        static $map = ['false' => 'assertFalse', 'null' => 'assertNull', 'true' => 'assertTrue'];
        return $this->fixAssert($map, $tokens, $index, $method);
    }
    /**
     * @param array<string, string> $map
     * @return int|null
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @param string $method
     */
    private function fixAssert(array $map, $tokens, $index, $method)
    {
        $functionsAnalyzer = new \PhpCsFixer\Tokenizer\Analyzer\FunctionsAnalyzer();
        $sequence = $tokens->findSequence([[\T_STRING, $method], '('], $index);
        if (null === $sequence) {
            return null;
        }
        $sequenceIndexes = \array_keys($sequence);
        if (!$functionsAnalyzer->isTheSameClassCall($tokens, $sequenceIndexes[0])) {
            return null;
        }
        $sequenceIndexes[2] = $tokens->getNextMeaningfulToken($sequenceIndexes[1]);
        $firstParameterToken = $tokens[$sequenceIndexes[2]];
        if (!$firstParameterToken->isNativeConstant()) {
            return $sequenceIndexes[2];
        }
        $sequenceIndexes[3] = $tokens->getNextMeaningfulToken($sequenceIndexes[2]);
        // return if first method argument is an expression, not value
        if (!$tokens[$sequenceIndexes[3]]->equals(',')) {
            return $sequenceIndexes[3];
        }
        $tokens[$sequenceIndexes[0]] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, $map[\strtolower($firstParameterToken->getContent())]]);
        $tokens->clearRange($sequenceIndexes[2], $tokens->getNextNonWhitespace($sequenceIndexes[3]) - 1);
        return $sequenceIndexes[3];
    }
}
