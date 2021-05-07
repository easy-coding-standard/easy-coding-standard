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
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class NoUnneededFinalMethodFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('A `final` class must not have `final` methods and `private` methods must not be `final`.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
final class Foo
{
    final public function foo1() {}
    final protected function bar() {}
    final private function baz() {}
}

class Bar
{
    final private function bar1() {}
}
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
final class Foo
{
    final private function baz() {}
}

class Bar
{
    final private function bar1() {}
}
', ['private_methods' => \false])], null, 'Risky when child class overrides a `private` method.');
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    public function isCandidate($tokens)
    {
        return $tokens->isAllTokenKindsFound([\T_CLASS, \T_FINAL]);
    }
    /**
     * @return bool
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param \SplFileInfo $file
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    protected function applyFix($file, $tokens)
    {
        $tokensCount = \count($tokens);
        for ($index = 0; $index < $tokensCount; ++$index) {
            if (!$tokens[$index]->isGivenKind(\T_CLASS)) {
                continue;
            }
            $classOpen = $tokens->getNextTokenOfKind($index, ['{']);
            $prevToken = $tokens[$tokens->getPrevMeaningfulToken($index)];
            $classIsFinal = $prevToken->isGivenKind(\T_FINAL);
            $this->fixClass($tokens, $classOpen, $classIsFinal);
        }
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
     */
    protected function createConfigurationDefinition()
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('private_methods', 'Private methods of non-`final` classes must not be declared `final`.'))->setAllowedTypes(['bool'])->setDefault(\true)->getOption()]);
    }
    /**
     * @return void
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $classOpenIndex
     * @param bool $classIsFinal
     */
    private function fixClass($tokens, $classOpenIndex, $classIsFinal)
    {
        $tokensCount = \count($tokens);
        for ($index = $classOpenIndex + 1; $index < $tokensCount; ++$index) {
            // Class end
            if ($tokens[$index]->equals('}')) {
                return;
            }
            // Skip method content
            if ($tokens[$index]->equals('{')) {
                $index = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $index);
                continue;
            }
            if (!$tokens[$index]->isGivenKind(\T_FINAL)) {
                continue;
            }
            if (!$classIsFinal && (!$this->isPrivateMethodOtherThanConstructor($tokens, $index, $classOpenIndex) || !$this->configuration['private_methods'])) {
                continue;
            }
            $tokens->clearAt($index);
            ++$index;
            if ($tokens[$index]->isWhitespace()) {
                $tokens->clearAt($index);
            }
        }
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @param int $classOpenIndex
     * @return bool
     */
    private function isPrivateMethodOtherThanConstructor($tokens, $index, $classOpenIndex)
    {
        $index = \max($classOpenIndex + 1, $tokens->getPrevTokenOfKind($index, [';', '{', '}']));
        $private = \false;
        while (!$tokens[$index]->isGivenKind(\T_FUNCTION)) {
            if ($tokens[$index]->isGivenKind(\T_PRIVATE)) {
                $private = \true;
            }
            $index = $tokens->getNextMeaningfulToken($index);
        }
        return $private && '__construct' !== \strtolower($tokens[$tokens->getNextMeaningfulToken($index)]->getContent());
    }
}
