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
namespace PhpCsFixer\Documentation;

use PhpCsFixer\Console\Command\HelpCommand;
use PhpCsFixer\Differ\FullDiffer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\DeprecatedFixerInterface;
use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerConfiguration\AliasedFixerOption;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\DeprecatedFixerOptionInterface;
use PhpCsFixer\FixerDefinition\CodeSampleInterface;
use PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\RuleSet\RuleSet;
use PhpCsFixer\RuleSet\RuleSets;
use PhpCsFixer\StdinFileInfo;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Utils;
/**
 * @internal
 */
final class FixerDocumentGenerator
{
    /**
     * @var DocumentationLocator
     */
    private $locator;
    /**
     * @var FullDiffer
     */
    private $differ;
    public function __construct(\PhpCsFixer\Documentation\DocumentationLocator $locator)
    {
        $this->locator = $locator;
        $this->differ = new \PhpCsFixer\Differ\FullDiffer();
    }
    public function generateFixerDocumentation(\PhpCsFixer\Fixer\FixerInterface $fixer) : string
    {
        $name = $fixer->getName();
        $title = "Rule ``{$name}``";
        $titleLine = \str_repeat('=', \strlen($title));
        $doc = "{$titleLine}\n{$title}\n{$titleLine}";
        $definition = $fixer->getDefinition();
        $doc .= "\n\n" . \PhpCsFixer\Documentation\RstUtils::toRst($definition->getSummary());
        $description = $definition->getDescription();
        if (null !== $description) {
            $description = \PhpCsFixer\Documentation\RstUtils::toRst($description);
            $doc .= <<<RST


Description
-----------

{$description}
RST;
        }
        $deprecationDescription = '';
        if ($fixer instanceof \PhpCsFixer\Fixer\DeprecatedFixerInterface) {
            $deprecationDescription = <<<'RST'

This rule is deprecated and will be removed on next major version
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
RST;
            $alternatives = $fixer->getSuccessorsNames();
            if (0 !== \count($alternatives)) {
                $deprecationDescription .= \PhpCsFixer\Documentation\RstUtils::toRst(\sprintf("\n\nYou should use %s instead.", \PhpCsFixer\Utils::naturalLanguageJoinWithBackticks($alternatives)), 0);
            }
        }
        $riskyDescription = '';
        $riskyDescriptionRaw = $definition->getRiskyDescription();
        if (null !== $riskyDescriptionRaw) {
            $riskyDescriptionRaw = \PhpCsFixer\Documentation\RstUtils::toRst($riskyDescriptionRaw, 0);
            $riskyDescription = <<<RST

Using this rule is risky
~~~~~~~~~~~~~~~~~~~~~~~~

{$riskyDescriptionRaw}
RST;
        }
        if ($deprecationDescription || $riskyDescription) {
            $warningsHeader = 'Warning';
            $warningsSeparator = '';
            if ($deprecationDescription && $riskyDescription) {
                $warningsHeader = 'Warnings';
                $warningsSeparator = "\n";
            }
            $warningsHeaderLine = \str_repeat('-', \strlen($warningsHeader));
            $doc .= "\n\n" . \implode("\n", \array_filter([$warningsHeader, $warningsHeaderLine, $deprecationDescription, $riskyDescription]));
        }
        if ($fixer instanceof \PhpCsFixer\Fixer\ConfigurableFixerInterface) {
            $doc .= <<<'RST'


Configuration
-------------
RST;
            $configurationDefinition = $fixer->getConfigurationDefinition();
            foreach ($configurationDefinition->getOptions() as $option) {
                $optionInfo = "``{$option->getName()}``";
                $optionInfo .= "\n" . \str_repeat('~', \strlen($optionInfo));
                if ($option instanceof \PhpCsFixer\FixerConfiguration\DeprecatedFixerOptionInterface) {
                    $deprecationMessage = \PhpCsFixer\Documentation\RstUtils::toRst($option->getDeprecationMessage());
                    $optionInfo .= "\n\n.. warning:: This option is deprecated and will be removed on next major version. {$deprecationMessage}";
                }
                $optionInfo .= "\n\n" . \PhpCsFixer\Documentation\RstUtils::toRst($option->getDescription());
                if ($option instanceof \PhpCsFixer\FixerConfiguration\AliasedFixerOption) {
                    $optionInfo .= "\n\n.. note:: The previous name of this option was ``{$option->getAlias()}`` but it is now deprecated and will be removed on next major version.";
                }
                $allowed = \PhpCsFixer\Console\Command\HelpCommand::getDisplayableAllowedValues($option);
                if (null === $allowed) {
                    $allowedKind = 'Allowed types';
                    $allowed = \array_map(static function ($value) : string {
                        return '``' . $value . '``';
                    }, $option->getAllowedTypes());
                } else {
                    $allowedKind = 'Allowed values';
                    foreach ($allowed as &$value) {
                        if ($value instanceof \PhpCsFixer\FixerConfiguration\AllowedValueSubset) {
                            $value = 'a subset of ``' . \PhpCsFixer\Console\Command\HelpCommand::toString($value->getAllowedValues()) . '``';
                        } else {
                            $value = '``' . \PhpCsFixer\Console\Command\HelpCommand::toString($value) . '``';
                        }
                    }
                }
                $allowed = \implode(', ', $allowed);
                $optionInfo .= "\n\n{$allowedKind}: {$allowed}";
                if ($option->hasDefault()) {
                    $default = \PhpCsFixer\Console\Command\HelpCommand::toString($option->getDefault());
                    $optionInfo .= "\n\nDefault value: ``{$default}``";
                } else {
                    $optionInfo .= "\n\nThis option is required.";
                }
                $doc .= "\n\n{$optionInfo}";
            }
        }
        $samples = $definition->getCodeSamples();
        if (0 !== \count($samples)) {
            $doc .= <<<'RST'


Examples
--------
RST;
            foreach ($samples as $index => $sample) {
                $title = \sprintf('Example #%d', $index + 1);
                $titleLine = \str_repeat('~', \strlen($title));
                $doc .= "\n\n{$title}\n{$titleLine}";
                if ($fixer instanceof \PhpCsFixer\Fixer\ConfigurableFixerInterface) {
                    if (null === $sample->getConfiguration()) {
                        $doc .= "\n\n*Default* configuration.";
                    } else {
                        $doc .= \sprintf("\n\nWith configuration: ``%s``.", \PhpCsFixer\Console\Command\HelpCommand::toString($sample->getConfiguration()));
                    }
                }
                $doc .= "\n" . $this->generateSampleDiff($fixer, $sample, $index + 1, $name);
            }
        }
        $ruleSetConfigs = [];
        foreach (\PhpCsFixer\RuleSet\RuleSets::getSetDefinitionNames() as $set) {
            $ruleSet = new \PhpCsFixer\RuleSet\RuleSet([$set => \true]);
            if ($ruleSet->hasRule($name)) {
                $ruleSetConfigs[$set] = $ruleSet->getRuleConfiguration($name);
            }
        }
        if ([] !== $ruleSetConfigs) {
            $plural = 1 !== \count($ruleSetConfigs) ? 's' : '';
            $doc .= <<<RST


Rule sets
---------

The rule is part of the following rule set{$plural}:
RST;
            foreach ($ruleSetConfigs as $set => $config) {
                $ruleSetPath = $this->locator->getRuleSetsDocumentationFilePath($set);
                $ruleSetPath = \substr($ruleSetPath, \strrpos($ruleSetPath, '/'));
                $doc .= <<<RST


{$set}
  Using the `{$set} <./../../ruleSets{$ruleSetPath}>`_ rule set will enable the ``{$name}`` rule
RST;
                if (null !== $config) {
                    $doc .= " with the config below:\n\n  ``" . \PhpCsFixer\Console\Command\HelpCommand::toString($config) . '``';
                } elseif ($fixer instanceof \PhpCsFixer\Fixer\ConfigurableFixerInterface) {
                    $doc .= ' with the default config.';
                } else {
                    $doc .= '.';
                }
            }
        }
        return "{$doc}\n";
    }
    /**
     * @param FixerInterface[] $fixers
     */
    public function generateFixersDocumentationIndex(array $fixers) : string
    {
        $overrideGroups = ['PhpUnit' => 'PHPUnit', 'PhpTag' => 'PHP Tag', 'Phpdoc' => 'PHPDoc'];
        \usort($fixers, static function (\PhpCsFixer\Fixer\FixerInterface $a, \PhpCsFixer\Fixer\FixerInterface $b) : int {
            return \strcmp(\get_class($a), \get_class($b));
        });
        $documentation = <<<'RST'
=======================
List of Available Rules
=======================
RST;
        $currentGroup = null;
        foreach ($fixers as $fixer) {
            $namespace = \PhpCsFixer\Preg::replace('/^.*\\\\(.+)\\\\.+Fixer$/', '$1', \get_class($fixer));
            $group = $overrideGroups[$namespace] ?? \PhpCsFixer\Preg::replace('/(?<=[[:lower:]])(?=[[:upper:]])/', ' ', $namespace);
            if ($group !== $currentGroup) {
                $underline = \str_repeat('-', \strlen($group));
                $documentation .= "\n\n{$group}\n{$underline}\n";
                $currentGroup = $group;
            }
            $path = './' . $this->locator->getFixerDocumentationFileRelativePath($fixer);
            $attributes = [];
            if ($fixer instanceof \PhpCsFixer\Fixer\DeprecatedFixerInterface) {
                $attributes[] = 'deprecated';
            }
            if ($fixer->isRisky()) {
                $attributes[] = 'risky';
            }
            $attributes = 0 === \count($attributes) ? '' : ' *(' . \implode(', ', $attributes) . ')*';
            $summary = \str_replace('`', '``', $fixer->getDefinition()->getSummary());
            $documentation .= <<<RST

- `{$fixer->getName()} <{$path}>`_{$attributes}

  {$summary}
RST;
        }
        return "{$documentation}\n";
    }
    private function generateSampleDiff(\PhpCsFixer\Fixer\FixerInterface $fixer, \PhpCsFixer\FixerDefinition\CodeSampleInterface $sample, int $sampleNumber, string $ruleName) : string
    {
        if ($sample instanceof \PhpCsFixer\FixerDefinition\VersionSpecificCodeSampleInterface && !$sample->isSuitableFor(\PHP_VERSION_ID)) {
            $existingFile = @\file_get_contents($this->locator->getFixerDocumentationFilePath($fixer));
            if (\false !== $existingFile) {
                \PhpCsFixer\Preg::match("/\\RExample #{$sampleNumber}\\R.+?(?<diff>\\R\\.\\. code-block:: diff\\R\\R.*?)\\R(?:\\R\\S|\$)/s", $existingFile, $matches);
                if (isset($matches['diff'])) {
                    return $matches['diff'];
                }
            }
            $error = <<<RST

.. error::
   Cannot generate diff for code sample #{$sampleNumber} of rule {$ruleName}:
   the sample is not suitable for current version of PHP (%s).
RST;
            return \sprintf($error, \PHP_VERSION);
        }
        $old = $sample->getCode();
        $tokens = \PhpCsFixer\Tokenizer\Tokens::fromCode($old);
        $file = $sample instanceof \PhpCsFixer\FixerDefinition\FileSpecificCodeSampleInterface ? $sample->getSplFileInfo() : new \PhpCsFixer\StdinFileInfo();
        if ($fixer instanceof \PhpCsFixer\Fixer\ConfigurableFixerInterface) {
            $fixer->configure($sample->getConfiguration() ?? []);
        }
        $fixer->fix($file, $tokens);
        $diff = $this->differ->diff($old, $tokens->generateCode());
        $diff = \PhpCsFixer\Preg::replace('/@@[ \\+\\-\\d,]+@@\\n/', '', $diff);
        $diff = \PhpCsFixer\Preg::replace('/\\r/', '^M', $diff);
        $diff = \PhpCsFixer\Preg::replace('/^ $/m', '', $diff);
        $diff = \PhpCsFixer\Preg::replace('/\\n$/', '', $diff);
        $diff = \PhpCsFixer\Documentation\RstUtils::indent($diff, 3);
        return <<<RST

.. code-block:: diff

   {$diff}
RST;
    }
}
