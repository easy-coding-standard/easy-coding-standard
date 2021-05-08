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
namespace PhpCsFixer\Fixer\Casing;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author SpacePossum
 */
final class MagicMethodCasingFixer extends \PhpCsFixer\AbstractFixer
{
    private static $magicNames = ['__call' => '__call', '__callstatic' => '__callStatic', '__clone' => '__clone', '__construct' => '__construct', '__debuginfo' => '__debugInfo', '__destruct' => '__destruct', '__get' => '__get', '__invoke' => '__invoke', '__isset' => '__isset', '__serialize' => '__serialize', '__set' => '__set', '__set_state' => '__set_state', '__sleep' => '__sleep', '__tostring' => '__toString', '__unserialize' => '__unserialize', '__unset' => '__unset', '__wakeup' => '__wakeup'];
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Magic method definitions and calls must be using the correct casing.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
class Foo
{
    public function __Sleep()
    {
    }
}
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
$foo->__INVOKE(1);
')]);
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_STRING) && $tokens->isAnyTokenKindsFound(\array_merge([\T_FUNCTION, \T_DOUBLE_COLON], \PhpCsFixer\Tokenizer\Token::getObjectOperatorKinds()));
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $inClass = 0;
        $tokenCount = \count($tokens);
        for ($index = 1; $index < $tokenCount - 2; ++$index) {
            if (0 === $inClass && $tokens[$index]->isClassy()) {
                $inClass = 1;
                $index = $tokens->getNextTokenOfKind($index, ['{']);
                continue;
            }
            if (0 !== $inClass) {
                if ($tokens[$index]->equals('{')) {
                    ++$inClass;
                    continue;
                }
                if ($tokens[$index]->equals('}')) {
                    --$inClass;
                    continue;
                }
            }
            if (!$tokens[$index]->isGivenKind(\T_STRING)) {
                continue;
                // wrong type
            }
            $content = $tokens[$index]->getContent();
            if ('__' !== \substr($content, 0, 2)) {
                continue;
                // cheap look ahead
            }
            $name = \strtolower($content);
            if (!$this->isMagicMethodName($name)) {
                continue;
                // method name is not one of the magic ones we can fix
            }
            $nameInCorrectCasing = $this->getMagicMethodNameInCorrectCasing($name);
            if ($nameInCorrectCasing === $content) {
                continue;
                // method name is already in the correct casing, no fix needed
            }
            if ($this->isFunctionSignature($tokens, $index)) {
                if (0 !== $inClass) {
                    // this is a method definition we want to fix
                    $this->setTokenToCorrectCasing($tokens, $index, $nameInCorrectCasing);
                }
                continue;
            }
            if ($this->isMethodCall($tokens, $index)) {
                $this->setTokenToCorrectCasing($tokens, $index, $nameInCorrectCasing);
                continue;
            }
            if (('__callstatic' === $name || '__set_state' === $name) && $this->isStaticMethodCall($tokens, $index)) {
                $this->setTokenToCorrectCasing($tokens, $index, $nameInCorrectCasing);
            }
        }
    }
    /**
     * @param int $index
     * @return bool
     */
    private function isFunctionSignature(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $index = (int) $index;
        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        if (!$tokens[$prevIndex]->isGivenKind(\T_FUNCTION)) {
            return \false;
            // not a method signature
        }
        return $tokens[$tokens->getNextMeaningfulToken($index)]->equals('(');
    }
    /**
     * @param int $index
     * @return bool
     */
    private function isMethodCall(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $index = (int) $index;
        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        if (!$tokens[$prevIndex]->isObjectOperator()) {
            return \false;
            // not a "simple" method call
        }
        return $tokens[$tokens->getNextMeaningfulToken($index)]->equals('(');
    }
    /**
     * @param int $index
     * @return bool
     */
    private function isStaticMethodCall(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $index = (int) $index;
        $prevIndex = $tokens->getPrevMeaningfulToken($index);
        if (!$tokens[$prevIndex]->isGivenKind(\T_DOUBLE_COLON)) {
            return \false;
            // not a "simple" static method call
        }
        return $tokens[$tokens->getNextMeaningfulToken($index)]->equals('(');
    }
    /**
     * @param string $name
     * @return bool
     */
    private function isMagicMethodName($name)
    {
        $name = (string) $name;
        return isset(self::$magicNames[$name]);
    }
    /**
     * @param string $name name of a magic method
     * @return string
     */
    private function getMagicMethodNameInCorrectCasing($name)
    {
        $name = (string) $name;
        return self::$magicNames[$name];
    }
    /**
     * @return void
     * @param int $index
     * @param string $nameInCorrectCasing
     */
    private function setTokenToCorrectCasing(\PhpCsFixer\Tokenizer\Tokens $tokens, $index, $nameInCorrectCasing)
    {
        $index = (int) $index;
        $nameInCorrectCasing = (string) $nameInCorrectCasing;
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, $nameInCorrectCasing]);
    }
}
