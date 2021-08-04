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
namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractProxyFixer;
use PhpCsFixer\ConfigurationException\InvalidConfigurationException;
use PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
final class PhpdocTagCasingFixer extends \PhpCsFixer\AbstractProxyFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Fixes casing of PHPDoc tags.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @inheritdoc\n */\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @inheritdoc\n * @Foo\n */\n", ['tags' => ['foo']])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority() : int
    {
        return parent::getPriority();
    }
    public function configure(array $configuration) : void
    {
        parent::configure($configuration);
        $replacements = [];
        foreach ($this->configuration['tags'] as $tag) {
            $replacements[$tag] = $tag;
        }
        /** @var GeneralPhpdocTagRenameFixer $generalPhpdocTagRenameFixer */
        $generalPhpdocTagRenameFixer = $this->proxyFixers['general_phpdoc_tag_rename'];
        try {
            $generalPhpdocTagRenameFixer->configure(['fix_annotation' => \true, 'fix_inline' => \true, 'replacements' => $replacements, 'case_sensitive' => \false]);
        } catch (\PhpCsFixer\ConfigurationException\InvalidConfigurationException $exception) {
            throw new \PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException($this->getName(), \PhpCsFixer\Preg::replace('/^\\[.+?\\] /', '', $exception->getMessage()), $exception);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('tags', 'List of tags to fix with their expected casing.'))->setAllowedTypes(['array'])->setDefault(['inheritDoc'])->getOption()]);
    }
    /**
     * {@inheritdoc}
     */
    protected function createProxyFixers() : array
    {
        return [new \PhpCsFixer\Fixer\Phpdoc\GeneralPhpdocTagRenameFixer()];
    }
}
