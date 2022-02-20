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

use PhpCsFixer\AbstractFixer;
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
use ECSPrefix20220220\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use ECSPrefix20220220\Symfony\Component\OptionsResolver\Options;
final class GeneralPhpdocTagRenameFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Renames PHPDoc tags.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @inheritDocs\n * {@inheritdocs}\n */\n", ['replacements' => ['inheritDocs' => 'inheritDoc']]), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @inheritDocs\n * {@inheritdocs}\n */\n", ['replacements' => ['inheritDocs' => 'inheritDoc'], 'fix_annotation' => \false]), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @inheritDocs\n * {@inheritdocs}\n */\n", ['replacements' => ['inheritDocs' => 'inheritDoc'], 'fix_inline' => \false]), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n/**\n * @inheritDocs\n * {@inheritdocs}\n */\n", ['replacements' => ['inheritDocs' => 'inheritDoc'], 'case_sensitive' => \true])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAddMissingParamAnnotationFixer, PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority() : int
    {
        // must be run before PhpdocAddMissingParamAnnotationFixer
        return 11;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('fix_annotation', 'Whether annotation tags should be fixed.'))->setAllowedTypes(['bool'])->setDefault(\true)->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('fix_inline', 'Whether inline tags should be fixed.'))->setAllowedTypes(['bool'])->setDefault(\true)->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('replacements', 'A map of tags to replace.'))->setAllowedTypes(['array'])->setNormalizer(static function (\ECSPrefix20220220\Symfony\Component\OptionsResolver\Options $options, $value) : array {
            $normalizedValue = [];
            foreach ($value as $from => $to) {
                if (!\is_string($from)) {
                    throw new \ECSPrefix20220220\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException('Tag to replace must be a string.');
                }
                if (!\is_string($to)) {
                    throw new \ECSPrefix20220220\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Tag to replace to from "%s" must be a string.', $from));
                }
                if (1 !== \PhpCsFixer\Preg::match('#^\\S+$#', $to) || \strpos($to, '*/') !== \false) {
                    throw new \ECSPrefix20220220\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Tag "%s" cannot be replaced by invalid tag "%s".', $from, $to));
                }
                $from = \trim($from);
                $to = \trim($to);
                if (!$options['case_sensitive']) {
                    $lowercaseFrom = \strtolower($from);
                    if (isset($normalizedValue[$lowercaseFrom]) && $normalizedValue[$lowercaseFrom] !== $to) {
                        throw new \ECSPrefix20220220\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Tag "%s" cannot be configured to be replaced with several different tags when case sensitivity is off.', $from));
                    }
                    $from = $lowercaseFrom;
                }
                $normalizedValue[$from] = $to;
            }
            foreach ($normalizedValue as $from => $to) {
                if (isset($normalizedValue[$to]) && $normalizedValue[$to] !== $to) {
                    throw new \ECSPrefix20220220\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Cannot change tag "%1$s" to tag "%2$s", as the tag "%2$s" is configured to be replaced to "%3$s".', $from, $to, $normalizedValue[$to]));
                }
            }
            return $normalizedValue;
        })->setDefault([])->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('case_sensitive', 'Whether tags should be replaced only if they have exact same casing.'))->setAllowedTypes(['bool'])->setDefault(\false)->getOption()]);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        if (0 === \count($this->configuration['replacements'])) {
            return;
        }
        if (\true === $this->configuration['fix_annotation']) {
            if ($this->configuration['fix_inline']) {
                $regex = '/"[^"]*"(*SKIP)(*FAIL)|\\b(?<=@)(%s)\\b/';
            } else {
                $regex = '/"[^"]*"(*SKIP)(*FAIL)|(?<!\\{@)(?<=@)(%s)(?!\\})/';
            }
        } else {
            $regex = '/(?<={@)(%s)(?=[ \\t}])/';
        }
        $caseInsensitive = \false === $this->configuration['case_sensitive'];
        $replacements = $this->configuration['replacements'];
        $regex = \sprintf($regex, \implode('|', \array_keys($replacements)));
        if ($caseInsensitive) {
            $regex .= 'i';
        }
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, \PhpCsFixer\Preg::replaceCallback($regex, static function (array $matches) use($caseInsensitive, $replacements) {
                if ($caseInsensitive) {
                    $matches[1] = \strtolower($matches[1]);
                }
                return $replacements[$matches[1]];
            }, $token->getContent())]);
        }
    }
}
