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
use PhpCsFixer\DocBlock\Annotation;
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
use PhpCsFixer\Utils;
final class PhpdocTypesOrderFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Sorts PHPDoc types.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param string|null $bar
 */
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param null|string $bar
 */
', ['null_adjustment' => 'always_last']), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param null|string|int|\\Foo $bar
 */
', ['sort_algorithm' => 'alpha']), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param null|string|int|\\Foo $bar
 */
', ['sort_algorithm' => 'alpha', 'null_adjustment' => 'always_last']), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * @param null|string|int|\\Foo $bar
 */
', ['sort_algorithm' => 'alpha', 'null_adjustment' => 'none'])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocAnnotationWithoutDotFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority() : int
    {
        return 0;
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
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('sort_algorithm', 'The sorting algorithm to apply.'))->setAllowedValues(['alpha', 'none'])->setDefault('alpha')->getOption(), (new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('null_adjustment', 'Forces the position of `null` (overrides `sort_algorithm`).'))->setAllowedValues(['always_first', 'always_last', 'none'])->setDefault('always_first')->getOption()]);
    }
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $doc = new \PhpCsFixer\DocBlock\DocBlock($token->getContent());
            $annotations = $doc->getAnnotationsOfType(\PhpCsFixer\DocBlock\Annotation::getTagsWithTypes());
            if (!\count($annotations)) {
                continue;
            }
            foreach ($annotations as $annotation) {
                $types = $annotation->getTypes();
                // fix main types
                $annotation->setTypes($this->sortTypes($types));
                // fix @method parameters types
                $line = $doc->getLine($annotation->getStart());
                $line->setContent(\PhpCsFixer\Preg::replaceCallback('/(@method\\s+.+?\\s+\\w+\\()(.*)\\)/', function (array $matches) {
                    $sorted = \PhpCsFixer\Preg::replaceCallback('/([^\\s,]+)([\\s]+\\$[^\\s,]+)/', function (array $matches) {
                        return $this->sortJoinedTypes($matches[1]) . $matches[2];
                    }, $matches[2]);
                    return $matches[1] . $sorted . ')';
                }, $line->getContent()));
            }
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $doc->getContent()]);
        }
    }
    /**
     * @param string[] $types
     *
     * @return string[]
     */
    private function sortTypes(array $types) : array
    {
        foreach ($types as $index => $type) {
            $types[$index] = \PhpCsFixer\Preg::replaceCallback('/^([^<]+)<(?:([\\w\\|]+?|<?.*>)(,\\s*))?(.*)>$/', function (array $matches) {
                return $matches[1] . '<' . $this->sortJoinedTypes($matches[2]) . $matches[3] . $this->sortJoinedTypes($matches[4]) . '>';
            }, $type);
        }
        if ('alpha' === $this->configuration['sort_algorithm']) {
            $types = \PhpCsFixer\Utils::stableSort($types, static function (string $type) {
                return $type;
            }, static function (string $typeA, string $typeB) {
                $regexp = '/^\\??\\\\?/';
                return \strcasecmp(\PhpCsFixer\Preg::replace($regexp, '', $typeA), \PhpCsFixer\Preg::replace($regexp, '', $typeB));
            });
        }
        if ('none' !== $this->configuration['null_adjustment']) {
            $nulls = [];
            foreach ($types as $index => $type) {
                if (\PhpCsFixer\Preg::match('/^\\\\?null$/i', $type)) {
                    $nulls[$index] = $type;
                    unset($types[$index]);
                }
            }
            if (\count($nulls)) {
                if ('always_last' === $this->configuration['null_adjustment']) {
                    \array_push($types, ...$nulls);
                } else {
                    \array_unshift($types, ...$nulls);
                }
            }
        }
        return $types;
    }
    private function sortJoinedTypes(string $types) : string
    {
        $types = \array_filter(\PhpCsFixer\Preg::split('/([^|<{\\(]+(?:[<{].*[>}]|\\(.+\\)(?::.+)?)?)/', $types, -1, \PREG_SPLIT_DELIM_CAPTURE | \PREG_SPLIT_NO_EMPTY), static function (string $value) {
            return '|' !== $value;
        });
        return \implode('|', $this->sortTypes($types));
    }
}
