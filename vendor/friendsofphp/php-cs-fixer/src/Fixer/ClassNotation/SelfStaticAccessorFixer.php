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
namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
final class SelfStaticAccessorFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * @var TokensAnalyzer
     */
    private $tokensAnalyzer;
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Inside a `final` class or anonymous class `self` should be preferred to `static`.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
final class Sample
{
    private static $A = 1;

    public function getBar()
    {
        return static::class.static::test().static::$A;
    }

    private static function test()
    {
        return \'test\';
    }
}
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
final class Foo
{
    public function bar()
    {
        return new static();
    }
}
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
final class Foo
{
    public function isBar()
    {
        return $foo instanceof static;
    }
}
'), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php
$a = new class() {
    public function getBar()
    {
        return static::class;
    }
};
', new \PhpCsFixer\FixerDefinition\VersionSpecification(70000))]);
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    public function isCandidate($tokens)
    {
        return $tokens->isAllTokenKindsFound([\T_CLASS, \T_STATIC]) && $tokens->isAnyTokenKindsFound([\T_DOUBLE_COLON, \T_NEW, \T_INSTANCEOF]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after FinalInternalClassFixer, FunctionToConstantFixer, PhpUnitTestCaseStaticMethodCallsFixer.
     * @return int
     */
    public function getPriority()
    {
        return -10;
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param \SplFileInfo $file
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    protected function applyFix($file, $tokens)
    {
        $this->tokensAnalyzer = $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        $classIndex = $tokens->getNextTokenOfKind(0, [[\T_CLASS]]);
        while (null !== $classIndex) {
            if ($tokens[$tokens->getPrevMeaningfulToken($classIndex)]->isGivenKind(\T_FINAL) || $tokensAnalyzer->isAnonymousClass($classIndex)) {
                $classIndex = $this->fixClass($tokens, $classIndex);
            }
            $classIndex = $tokens->getNextTokenOfKind($classIndex, [[\T_CLASS]]);
        }
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @return int
     */
    private function fixClass($tokens, $index)
    {
        $index = $tokens->getNextTokenOfKind($index, ['{']);
        $classOpenCount = 1;
        while ($classOpenCount > 0) {
            ++$index;
            if ($tokens[$index]->equals('{')) {
                ++$classOpenCount;
                continue;
            }
            if ($tokens[$index]->equals('}')) {
                --$classOpenCount;
                continue;
            }
            if ($tokens[$index]->isGivenKind(\T_FUNCTION)) {
                // do not fix inside lambda
                if ($this->tokensAnalyzer->isLambda($index)) {
                    // figure out where the lambda starts
                    $index = $tokens->getNextTokenOfKind($index, ['{']);
                    $openCount = 1;
                    do {
                        $index = $tokens->getNextTokenOfKind($index, ['}', '{', [\T_CLASS]]);
                        if ($tokens[$index]->equals('}')) {
                            --$openCount;
                        } elseif ($tokens[$index]->equals('{')) {
                            ++$openCount;
                        } else {
                            $index = $this->fixClass($tokens, $index);
                        }
                    } while ($openCount > 0);
                }
                continue;
            }
            if ($tokens[$index]->isGivenKind([\T_NEW, \T_INSTANCEOF])) {
                $index = $tokens->getNextMeaningfulToken($index);
                if ($tokens[$index]->isGivenKind(\T_STATIC)) {
                    $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, 'self']);
                }
                continue;
            }
            if (!$tokens[$index]->isGivenKind(\T_STATIC)) {
                continue;
            }
            $staticIndex = $index;
            $index = $tokens->getNextMeaningfulToken($index);
            if (!$tokens[$index]->isGivenKind(\T_DOUBLE_COLON)) {
                continue;
            }
            $tokens[$staticIndex] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, 'self']);
        }
        return $index;
    }
}
