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

namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
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
use PhpCsFixer\Utils;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 */
final class PhpUnitMethodCasingFixer extends AbstractPhpUnitFixer implements ConfigurableFixerInterface
{
    /**
     * @internal
     */
    const CAMEL_CASE = 'camel_case';

    /**
     * @internal
     */
    const SNAKE_CASE = 'snake_case';

    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'Enforce camel (or snake) case for PHPUnit test methods, following configuration.',
            [
                new CodeSample(
                    '<?php
class MyTest extends \\PhpUnit\\FrameWork\\TestCase
{
    public function test_my_code() {}
}
'
                ),
                new CodeSample(
                    '<?php
class MyTest extends \\PhpUnit\\FrameWork\\TestCase
{
    public function testMyCode() {}
}
',
                    ['case' => self::SNAKE_CASE]
                ),
            ]
        );
    }

    /**
     * {@inheritdoc}
     *
     * Must run after PhpUnitTestAnnotationFixer.
     * @return int
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
     */
    protected function createConfigurationDefinition()
    {
        return new FixerConfigurationResolver([
            (new FixerOptionBuilder('case', 'Apply camel or snake case to test methods'))
                ->setAllowedValues([self::CAMEL_CASE, self::SNAKE_CASE])
                ->setDefault(self::CAMEL_CASE)
                ->getOption(),
        ]);
    }

    /**
     * {@inheritdoc}
     * @return void
     * @param int $startIndex
     * @param int $endIndex
     */
    protected function applyPhpUnitClassFix(Tokens $tokens, $startIndex, $endIndex)
    {
        $startIndex = (int) $startIndex;
        $endIndex = (int) $endIndex;
        for ($index = $endIndex - 1; $index > $startIndex; --$index) {
            if (!$this->isTestMethod($tokens, $index)) {
                continue;
            }

            $functionNameIndex = $tokens->getNextMeaningfulToken($index);
            $functionName = $tokens[$functionNameIndex]->getContent();
            $newFunctionName = $this->updateMethodCasing($functionName);

            if ($newFunctionName !== $functionName) {
                $tokens[$functionNameIndex] = new Token([T_STRING, $newFunctionName]);
            }

            $docBlockIndex = $this->getDocBlockIndex($tokens, $index);

            if ($this->isPHPDoc($tokens, $docBlockIndex)) {
                $this->updateDocBlock($tokens, $docBlockIndex);
            }
        }
    }

    /**
     * @param string $functionName
     * @return string
     */
    private function updateMethodCasing($functionName)
    {
        $functionName = (string) $functionName;
        $parts = explode('::', $functionName);

        $functionNamePart = array_pop($parts);

        if (self::CAMEL_CASE === $this->configuration['case']) {
            $newFunctionNamePart = $functionNamePart;
            $newFunctionNamePart = ucwords($newFunctionNamePart, '_');
            $newFunctionNamePart = str_replace('_', '', $newFunctionNamePart);
            $newFunctionNamePart = lcfirst($newFunctionNamePart);
        } else {
            $newFunctionNamePart = Utils::camelCaseToUnderscore($functionNamePart);
        }

        $parts[] = $newFunctionNamePart;

        return implode('::', $parts);
    }

    /**
     * @param int $index
     * @return bool
     */
    private function isTestMethod(Tokens $tokens, $index)
    {
        $index = (int) $index;
        // Check if we are dealing with a (non abstract, non lambda) function
        if (!$this->isMethod($tokens, $index)) {
            return false;
        }

        // if the function name starts with test it's a test
        $functionNameIndex = $tokens->getNextMeaningfulToken($index);
        $functionName = $tokens[$functionNameIndex]->getContent();

        if ($this->startsWith('test', $functionName)) {
            return true;
        }

        $docBlockIndex = $this->getDocBlockIndex($tokens, $index);

        return
            $this->isPHPDoc($tokens, $docBlockIndex) // If the function doesn't have test in its name, and no doc block, it's not a test
            && false !== strpos($tokens[$docBlockIndex]->getContent(), '@test')
        ;
    }

    /**
     * @param int $index
     * @return bool
     */
    private function isMethod(Tokens $tokens, $index)
    {
        $index = (int) $index;
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        return $tokens[$index]->isGivenKind(T_FUNCTION) && !$tokensAnalyzer->isLambda($index);
    }

    /**
     * @param string $needle
     * @param string $haystack
     * @return bool
     */
    private function startsWith($needle, $haystack)
    {
        $needle = (string) $needle;
        $haystack = (string) $haystack;
        return substr($haystack, 0, \strlen($needle)) === $needle;
    }

    /**
     * @return void
     * @param int $docBlockIndex
     */
    private function updateDocBlock(Tokens $tokens, $docBlockIndex)
    {
        $docBlockIndex = (int) $docBlockIndex;
        $doc = new DocBlock($tokens[$docBlockIndex]->getContent());
        $lines = $doc->getLines();

        $docBlockNeedsUpdate = false;
        for ($inc = 0; $inc < \count($lines); ++$inc) {
            $lineContent = $lines[$inc]->getContent();
            if (false === strpos($lineContent, '@depends')) {
                continue;
            }

            $newLineContent = Preg::replaceCallback('/(@depends\s+)(.+)(\b)/', function (array $matches) {
                return sprintf(
                    '%s%s%s',
                    $matches[1],
                    $this->updateMethodCasing($matches[2]),
                    $matches[3]
                );
            }, $lineContent);

            if ($newLineContent !== $lineContent) {
                $lines[$inc] = new Line($newLineContent);
                $docBlockNeedsUpdate = true;
            }
        }

        if ($docBlockNeedsUpdate) {
            $lines = implode('', $lines);
            $tokens[$docBlockIndex] = new Token([T_DOC_COMMENT, $lines]);
        }
    }
}
