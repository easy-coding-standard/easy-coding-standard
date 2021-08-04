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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * @author Gert de Pagter
 */
final class PhpUnitSetUpTearDownVisibilityFixer extends \PhpCsFixer\Fixer\AbstractPhpUnitFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Changes the visibility of the `setUp()` and `tearDown()` functions of PHPUnit to `protected`, to match the PHPUnit TestCase.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
final class MyTest extends \\PHPUnit_Framework_TestCase
{
    private $hello;
    public function setUp()
    {
        $this->hello = "hello";
    }

    public function tearDown()
    {
        $this->hello = null;
    }
}
')], null, 'This fixer may change functions named `setUp()` or `tearDown()` outside of PHPUnit tests, ' . 'when a class is wrongly seen as a PHPUnit test.');
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
        $counter = 0;
        $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            if (2 === $counter) {
                break;
                // we've seen both method we are interested in, so stop analyzing this class
            }
            if (!$this->isSetupOrTearDownMethod($tokens, $i)) {
                continue;
            }
            ++$counter;
            $visibility = $tokensAnalyzer->getMethodAttributes($i)['visibility'];
            if (\T_PUBLIC === $visibility) {
                $index = $tokens->getPrevTokenOfKind($i, [[\T_PUBLIC]]);
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_PROTECTED, 'protected']);
                continue;
            }
            if (null === $visibility) {
                $tokens->insertAt($i, [new \PhpCsFixer\Tokenizer\Token([\T_PROTECTED, 'protected']), new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])]);
            }
        }
    }
    private function isSetupOrTearDownMethod(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : bool
    {
        $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        $isMethod = $tokens[$index]->isGivenKind(\T_FUNCTION) && !$tokensAnalyzer->isLambda($index);
        if (!$isMethod) {
            return \false;
        }
        $functionNameIndex = $tokens->getNextMeaningfulToken($index);
        $functionName = \strtolower($tokens[$functionNameIndex]->getContent());
        return 'setup' === $functionName || 'teardown' === $functionName;
    }
}
