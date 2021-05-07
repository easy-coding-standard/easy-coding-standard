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
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * Fixer for constants case.
 *
 * @author Pol Dellaiera <pol.dellaiera@protonmail.com>
 */
final class ConstantCaseFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * Hold the function that will be used to convert the constants.
     *
     * @var callable
     */
    private $fixFunction;
    /**
     * {@inheritdoc}
     * @return void
     */
    public function configure(array $configuration)
    {
        parent::configure($configuration);
        if ('lower' === $this->configuration['case']) {
            $this->fixFunction = static function (string $content) {
                return \strtolower($content);
            };
        }
        if ('upper' === $this->configuration['case']) {
            $this->fixFunction = static function (string $content) {
                return \strtoupper($content);
            };
        }
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('The PHP constants `true`, `false`, and `null` MUST be written using the correct casing.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = FALSE;\n\$b = True;\n\$c = nuLL;\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\$a = FALSE;\n\$b = True;\n\$c = nuLL;\n", ['case' => 'upper'])]);
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    public function isCandidate($tokens)
    {
        return $tokens->isTokenKindFound(\T_STRING);
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
     */
    protected function createConfigurationDefinition()
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('case', 'Whether to use the `upper` or `lower` case syntax.'))->setAllowedValues(['upper', 'lower'])->setDefault('lower')->getOption()]);
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param \SplFileInfo $file
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    protected function applyFix($file, $tokens)
    {
        $fixFunction = $this->fixFunction;
        foreach ($tokens as $index => $token) {
            if (!$token->isNativeConstant()) {
                continue;
            }
            if ($this->isNeighbourAccepted($tokens, $tokens->getPrevMeaningfulToken($index)) && $this->isNeighbourAccepted($tokens, $tokens->getNextMeaningfulToken($index))) {
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([$token->getId(), $fixFunction($token->getContent())]);
            }
        }
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @return bool
     */
    private function isNeighbourAccepted($tokens, $index)
    {
        static $forbiddenTokens = null;
        if (null === $forbiddenTokens) {
            $forbiddenTokens = \array_merge([\T_AS, \T_CLASS, \T_CONST, \T_EXTENDS, \T_IMPLEMENTS, \T_INSTANCEOF, \T_INSTEADOF, \T_INTERFACE, \T_NEW, \T_NS_SEPARATOR, \T_PAAMAYIM_NEKUDOTAYIM, \T_TRAIT, \T_USE, \PhpCsFixer\Tokenizer\CT::T_USE_TRAIT, \PhpCsFixer\Tokenizer\CT::T_USE_LAMBDA], \PhpCsFixer\Tokenizer\Token::getObjectOperatorKinds());
        }
        $token = $tokens[$index];
        if ($token->equalsAny(['{', '}'])) {
            return \false;
        }
        return !$token->isGivenKind($forbiddenTokens);
    }
}
