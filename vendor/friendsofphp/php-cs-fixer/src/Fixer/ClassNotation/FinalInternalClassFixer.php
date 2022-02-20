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
namespace PhpCsFixer\Fixer\ClassNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
use ECSPrefix20220220\Symfony\Component\OptionsResolver\Options;
/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class FinalInternalClassFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function configure(array $configuration) : void
    {
        parent::configure($configuration);
        $intersect = \array_intersect_assoc($this->configuration['annotation_include'], $this->configuration['annotation_exclude']);
        if (\count($intersect) > 0) {
            throw new \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException($this->getName(), \sprintf('Annotation cannot be used in both the include and exclude list, got duplicates: "%s".', \implode('", "', \array_keys($intersect))));
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Internal classes should be `final`.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @internal\n */\nclass Sample\n{\n}\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @CUSTOM\n */\nclass A{}\n\n/**\n * @CUSTOM\n * @not-fix\n */\nclass B{}\n", ['annotation_include' => ['@Custom'], 'annotation_exclude' => ['@not-fix']])], null, 'Changing classes to `final` might cause code execution to break.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before ProtectedToPrivateFixer, SelfStaticAccessorFixer.
     * Must run after PhpUnitInternalClassFixer.
     */
    public function getPriority() : int
    {
        return 67;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_CLASS);
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
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_CLASS) || $tokensAnalyzer->isAnonymousClass($index) || !$this->isClassCandidate($tokens, $index)) {
                continue;
            }
            // make class final
            $tokens->insertAt($index, [new \PhpCsFixer\Tokenizer\Token([\T_FINAL, 'final']), new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' '])]);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        $annotationsAsserts = [static function (array $values) : bool {
            foreach ($values as $value) {
                if (!\is_string($value) || '' === $value) {
                    return \false;
                }
            }
            return \true;
        }];
        $annotationsNormalizer = static function (\ECSPrefix20220220\Symfony\Component\OptionsResolver\Options $options, array $value) : array {
            $newValue = [];
            foreach ($value as $key) {
                if ('@' === $key[0]) {
                    $key = \substr($key, 1);
                }
                $newValue[\strtolower($key)] = \true;
            }
            return $newValue;
        };
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('annotation_include', 'Class level annotations tags that must be set in order to fix the class. (case insensitive)'))->setAllowedTypes(['array'])->setAllowedValues($annotationsAsserts)->setDefault(['@internal'])->setNormalizer($annotationsNormalizer)->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('annotation_exclude', 'Class level annotations tags that must be omitted to fix the class, even if all of the white list ones are used as well. (case insensitive)'))->setAllowedTypes(['array'])->setAllowedValues($annotationsAsserts)->setDefault(['@final', '@Entity', 'ECSPrefix20220220\\@ORM\\Entity', 'ECSPrefix20220220\\@ORM\\Mapping\\Entity', 'ECSPrefix20220220\\@Mapping\\Entity', '@Document', 'ECSPrefix20220220\\@ODM\\Document'])->setNormalizer($annotationsNormalizer)->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('consider_absent_docblock_as_internal_class', 'Should classes without any DocBlock be fixed to final?'))->setAllowedTypes(['bool'])->setDefault(\false)->getOption()]);
    }
    /**
     * @param int $index T_CLASS index
     */
    private function isClassCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : bool
    {
        if ($tokens[$tokens->getPrevMeaningfulToken($index)]->isGivenKind([\T_ABSTRACT, \T_FINAL])) {
            return \false;
            // ignore class; it is abstract or already final
        }
        $docToken = $tokens[$tokens->getPrevNonWhitespace($index)];
        if (!$docToken->isGivenKind(\T_DOC_COMMENT)) {
            return $this->configuration['consider_absent_docblock_as_internal_class'];
        }
        $doc = new \PhpCsFixer\DocBlock\DocBlock($docToken->getContent());
        $tags = [];
        foreach ($doc->getAnnotations() as $annotation) {
            if (1 !== \PhpCsFixer\Preg::match('/@\\S+(?=\\s|$)/', $annotation->getContent(), $matches)) {
                continue;
            }
            $tag = \strtolower(\substr(\array_shift($matches), 1));
            foreach ($this->configuration['annotation_exclude'] as $tagStart => $true) {
                if (\strncmp($tag, $tagStart, \strlen($tagStart)) === 0) {
                    return \false;
                    // ignore class: class-level PHPDoc contains tag that has been excluded through configuration
                }
            }
            $tags[$tag] = \true;
        }
        foreach ($this->configuration['annotation_include'] as $tag => $true) {
            if (!isset($tags[$tag])) {
                return \false;
                // ignore class: class-level PHPDoc does not contain all tags that has been included through configuration
            }
        }
        return \true;
    }
}
