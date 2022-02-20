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
namespace PhpCsFixer\Fixer\DoctrineAnnotation;

use ECSPrefix20220220\Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\AbstractDoctrineAnnotationFixer;
use PhpCsFixer\Doctrine\Annotation\Token;
use PhpCsFixer\Doctrine\Annotation\Tokens;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
/**
 * Adds braces to Doctrine annotations when missing.
 */
final class DoctrineAnnotationBracesFixer extends \PhpCsFixer\AbstractDoctrineAnnotationFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Doctrine annotations without arguments must use the configured syntax.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @Foo()\n */\nclass Bar {}\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @Foo\n */\nclass Bar {}\n", ['syntax' => 'with_braces'])]);
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver(\array_merge(parent::createConfigurationDefinition()->getOptions(), [(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('syntax', 'Whether to add or remove braces.'))->setAllowedValues(['with_braces', 'without_braces'])->setDefault('without_braces')->getOption()]));
    }
    /**
     * {@inheritdoc}
     */
    protected function fixAnnotations(\PhpCsFixer\Doctrine\Annotation\Tokens $doctrineAnnotationTokens) : void
    {
        if ('without_braces' === $this->configuration['syntax']) {
            $this->removesBracesFromAnnotations($doctrineAnnotationTokens);
        } else {
            $this->addBracesToAnnotations($doctrineAnnotationTokens);
        }
    }
    private function addBracesToAnnotations(\PhpCsFixer\Doctrine\Annotation\Tokens $tokens) : void
    {
        foreach ($tokens as $index => $token) {
            if (!$tokens[$index]->isType(\ECSPrefix20220220\Doctrine\Common\Annotations\DocLexer::T_AT)) {
                continue;
            }
            $braceIndex = $tokens->getNextMeaningfulToken($index + 1);
            if (null !== $braceIndex && $tokens[$braceIndex]->isType(\ECSPrefix20220220\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS)) {
                continue;
            }
            $tokens->insertAt($index + 2, new \PhpCsFixer\Doctrine\Annotation\Token(\ECSPrefix20220220\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS, '('));
            $tokens->insertAt($index + 3, new \PhpCsFixer\Doctrine\Annotation\Token(\ECSPrefix20220220\Doctrine\Common\Annotations\DocLexer::T_CLOSE_PARENTHESIS, ')'));
        }
    }
    private function removesBracesFromAnnotations(\PhpCsFixer\Doctrine\Annotation\Tokens $tokens) : void
    {
        for ($index = 0, $max = \count($tokens); $index < $max; ++$index) {
            if (!$tokens[$index]->isType(\ECSPrefix20220220\Doctrine\Common\Annotations\DocLexer::T_AT)) {
                continue;
            }
            $openBraceIndex = $tokens->getNextMeaningfulToken($index + 1);
            if (null === $openBraceIndex) {
                continue;
            }
            if (!$tokens[$openBraceIndex]->isType(\ECSPrefix20220220\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS)) {
                continue;
            }
            $closeBraceIndex = $tokens->getNextMeaningfulToken($openBraceIndex);
            if (null === $closeBraceIndex) {
                continue;
            }
            if (!$tokens[$closeBraceIndex]->isType(\ECSPrefix20220220\Doctrine\Common\Annotations\DocLexer::T_CLOSE_PARENTHESIS)) {
                continue;
            }
            for ($currentIndex = $index + 2; $currentIndex <= $closeBraceIndex; ++$currentIndex) {
                $tokens[$currentIndex]->clear();
            }
        }
    }
}
