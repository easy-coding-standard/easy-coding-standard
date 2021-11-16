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
namespace PhpCsFixer\Fixer\PhpUnit;

use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\Fixer\AbstractPhpUnitFixer;
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * @author Gert de Pagter
 */
final class PhpUnitTestAnnotationFixer extends \PhpCsFixer\Fixer\AbstractPhpUnitFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface, \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
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
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Adds or removes @test annotations from tests, following configuration.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
class Test extends \\PhpUnit\\FrameWork\\TestCase
{
    /**
     * @test
     */
    public function itDoesSomething() {} }' . $this->whitespacesConfig->getLineEnding()), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
class Test extends \\PhpUnit\\FrameWork\\TestCase
{
public function testItDoesSomething() {}}' . $this->whitespacesConfig->getLineEnding(), ['style' => 'annotation'])], null, 'This fixer may change the name of your tests, and could cause incompatibility with' . ' abstract classes or interfaces.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before NoEmptyPhpdocFixer, PhpUnitMethodCasingFixer, PhpdocTrimFixer.
     */
    public function getPriority() : int
    {
        return 10;
    }
    /**
     * {@inheritdoc}
     */
    protected function applyPhpUnitClassFix(\PhpCsFixer\Tokenizer\Tokens $tokens, int $startIndex, int $endIndex) : void
    {
        if ('annotation' === $this->configuration['style']) {
            $this->applyTestAnnotation($tokens, $startIndex, $endIndex);
        } else {
            $this->applyTestPrefix($tokens, $startIndex, $endIndex);
        }
    }
    /**
     * {@inheritdoc}
     */
    protected function createConfigurationDefinition() : \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('style', 'Whether to use the @test annotation or not.'))->setAllowedValues(['prefix', 'annotation'])->setDefault('prefix')->getOption()]);
    }
    private function applyTestAnnotation(\PhpCsFixer\Tokenizer\Tokens $tokens, int $startIndex, int $endIndex) : void
    {
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            if (!$this->isTestMethod($tokens, $i)) {
                continue;
            }
            $functionNameIndex = $tokens->getNextMeaningfulToken($i);
            $functionName = $tokens[$functionNameIndex]->getContent();
            if ($this->hasTestPrefix($functionName) && !$this->hasProperTestAnnotation($tokens, $i)) {
                $newFunctionName = $this->removeTestPrefix($functionName);
                $tokens[$functionNameIndex] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, $newFunctionName]);
            }
            $docBlockIndex = $this->getDocBlockIndex($tokens, $i);
            if ($this->isPHPDoc($tokens, $docBlockIndex)) {
                $lines = $this->updateDocBlock($tokens, $docBlockIndex);
                $lines = $this->addTestAnnotation($lines, $tokens, $docBlockIndex);
                $lines = \implode('', $lines);
                $tokens[$docBlockIndex] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $lines]);
            } else {
                // Create a new docblock if it didn't have one before;
                $this->createDocBlock($tokens, $docBlockIndex);
            }
        }
    }
    private function applyTestPrefix(\PhpCsFixer\Tokenizer\Tokens $tokens, int $startIndex, int $endIndex) : void
    {
        for ($i = $endIndex - 1; $i > $startIndex; --$i) {
            // We explicitly check again if the function has a doc block to save some time.
            if (!$this->isTestMethod($tokens, $i)) {
                continue;
            }
            $docBlockIndex = $this->getDocBlockIndex($tokens, $i);
            if (!$this->isPHPDoc($tokens, $docBlockIndex)) {
                continue;
            }
            $lines = $this->updateDocBlock($tokens, $docBlockIndex);
            $lines = \implode('', $lines);
            $tokens[$docBlockIndex] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $lines]);
            $functionNameIndex = $tokens->getNextMeaningfulToken($i);
            $functionName = $tokens[$functionNameIndex]->getContent();
            if ($this->hasTestPrefix($functionName)) {
                continue;
            }
            $newFunctionName = $this->addTestPrefix($functionName);
            $tokens[$functionNameIndex] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, $newFunctionName]);
        }
    }
    private function isTestMethod(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : bool
    {
        // Check if we are dealing with a (non-abstract, non-lambda) function
        if (!$this->isMethod($tokens, $index)) {
            return \false;
        }
        // if the function name starts with test it is a test
        $functionNameIndex = $tokens->getNextMeaningfulToken($index);
        $functionName = $tokens[$functionNameIndex]->getContent();
        if ($this->hasTestPrefix($functionName)) {
            return \true;
        }
        $docBlockIndex = $this->getDocBlockIndex($tokens, $index);
        // If the function doesn't have test in its name, and no doc block, it is not a test
        return $this->isPHPDoc($tokens, $docBlockIndex) && \strpos($tokens[$docBlockIndex]->getContent(), '@test') !== \false;
    }
    private function isMethod(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : bool
    {
        $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        return $tokens[$index]->isGivenKind(\T_FUNCTION) && !$tokensAnalyzer->isLambda($index);
    }
    private function hasTestPrefix(string $functionName) : bool
    {
        return \strncmp($functionName, 'test', \strlen('test')) === 0;
    }
    private function hasProperTestAnnotation(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index) : bool
    {
        $docBlockIndex = $this->getDocBlockIndex($tokens, $index);
        $doc = $tokens[$docBlockIndex]->getContent();
        return 1 === \PhpCsFixer\Preg::match('/\\*\\s+@test\\b/', $doc);
    }
    private function removeTestPrefix(string $functionName) : string
    {
        $remainder = \PhpCsFixer\Preg::replace('/^test(?=[A-Z_])_?/', '', $functionName);
        if ('' === $remainder) {
            return $functionName;
        }
        return \lcfirst($remainder);
    }
    private function addTestPrefix(string $functionName) : string
    {
        return 'test' . \ucfirst($functionName);
    }
    private function createDocBlock(\PhpCsFixer\Tokenizer\Tokens $tokens, int $docBlockIndex) : void
    {
        $lineEnd = $this->whitespacesConfig->getLineEnding();
        $originalIndent = \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $tokens->getNextNonWhitespace($docBlockIndex));
        $toInsert = [new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, '/**' . $lineEnd . "{$originalIndent} * @test" . $lineEnd . "{$originalIndent} */"]), new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $lineEnd . $originalIndent])];
        $index = $tokens->getNextMeaningfulToken($docBlockIndex);
        $tokens->insertAt($index, $toInsert);
    }
    /**
     * @return Line[]
     */
    private function updateDocBlock(\PhpCsFixer\Tokenizer\Tokens $tokens, int $docBlockIndex) : array
    {
        $doc = new \PhpCsFixer\DocBlock\DocBlock($tokens[$docBlockIndex]->getContent());
        $lines = $doc->getLines();
        return $this->updateLines($lines, $tokens, $docBlockIndex);
    }
    /**
     * @param Line[] $lines
     *
     * @return Line[]
     */
    private function updateLines(array $lines, \PhpCsFixer\Tokenizer\Tokens $tokens, int $docBlockIndex) : array
    {
        $needsAnnotation = 'annotation' === $this->configuration['style'];
        $doc = new \PhpCsFixer\DocBlock\DocBlock($tokens[$docBlockIndex]->getContent());
        for ($i = 0; $i < \count($lines); ++$i) {
            // If we need to add test annotation and it is a single line comment we need to deal with that separately
            if ($needsAnnotation && ($lines[$i]->isTheStart() && $lines[$i]->isTheEnd())) {
                if (!$this->doesDocBlockContainTest($doc)) {
                    $lines = $this->splitUpDocBlock($lines, $tokens, $docBlockIndex);
                    return $this->updateLines($lines, $tokens, $docBlockIndex);
                }
                // One we split it up, we run the function again, so we deal with other things in a proper way
            }
            if (!$needsAnnotation && \strpos($lines[$i]->getContent(), ' @test') !== \false && \strpos($lines[$i]->getContent(), '@testWith') === \false && \strpos($lines[$i]->getContent(), '@testdox') === \false) {
                // We remove @test from the doc block
                $lines[$i] = new \PhpCsFixer\DocBlock\Line(\str_replace(' @test', '', $lines[$i]->getContent()));
            }
            // ignore the line if it isn't @depends
            if (\strpos($lines[$i]->getContent(), '@depends') === \false) {
                continue;
            }
            $lines[$i] = $this->updateDependsAnnotation($lines[$i]);
        }
        return $lines;
    }
    /**
     * Take a one line doc block, and turn it into a multi line doc block.
     *
     * @param Line[] $lines
     *
     * @return Line[]
     */
    private function splitUpDocBlock(array $lines, \PhpCsFixer\Tokenizer\Tokens $tokens, int $docBlockIndex) : array
    {
        $lineContent = $this->getSingleLineDocBlockEntry($lines);
        $lineEnd = $this->whitespacesConfig->getLineEnding();
        $originalIndent = \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $tokens->getNextNonWhitespace($docBlockIndex));
        return [new \PhpCsFixer\DocBlock\Line('/**' . $lineEnd), new \PhpCsFixer\DocBlock\Line($originalIndent . ' * ' . $lineContent . $lineEnd), new \PhpCsFixer\DocBlock\Line($originalIndent . ' */')];
    }
    /**
     * @todo check whether it's doable to use \PhpCsFixer\DocBlock\DocBlock::getSingleLineDocBlockEntry instead
     *
     * @param Line[] $lines
     */
    private function getSingleLineDocBlockEntry(array $lines) : string
    {
        $line = $lines[0];
        $line = \str_replace('*/', '', $line->getContent());
        $line = \trim($line);
        $line = \str_split($line);
        $i = \count($line);
        do {
            --$i;
        } while ('*' !== $line[$i] && '*' !== $line[$i - 1] && '/' !== $line[$i - 2]);
        if (' ' === $line[$i]) {
            ++$i;
        }
        $line = \array_slice($line, $i);
        return \implode('', $line);
    }
    /**
     * Updates the depends tag on the current doc block.
     */
    private function updateDependsAnnotation(\PhpCsFixer\DocBlock\Line $line) : \PhpCsFixer\DocBlock\Line
    {
        if ('annotation' === $this->configuration['style']) {
            return $this->removeTestPrefixFromDependsAnnotation($line);
        }
        return $this->addTestPrefixToDependsAnnotation($line);
    }
    private function removeTestPrefixFromDependsAnnotation(\PhpCsFixer\DocBlock\Line $line) : \PhpCsFixer\DocBlock\Line
    {
        $line = \str_split($line->getContent());
        $dependsIndex = $this->findWhereDependsFunctionNameStarts($line);
        $dependsFunctionName = \implode('', \array_slice($line, $dependsIndex));
        if ($this->hasTestPrefix($dependsFunctionName)) {
            $dependsFunctionName = $this->removeTestPrefix($dependsFunctionName);
        }
        \array_splice($line, $dependsIndex);
        return new \PhpCsFixer\DocBlock\Line(\implode('', $line) . $dependsFunctionName);
    }
    private function addTestPrefixToDependsAnnotation(\PhpCsFixer\DocBlock\Line $line) : \PhpCsFixer\DocBlock\Line
    {
        $line = \str_split($line->getContent());
        $dependsIndex = $this->findWhereDependsFunctionNameStarts($line);
        $dependsFunctionName = \implode('', \array_slice($line, $dependsIndex));
        if (!$this->hasTestPrefix($dependsFunctionName)) {
            $dependsFunctionName = $this->addTestPrefix($dependsFunctionName);
        }
        \array_splice($line, $dependsIndex);
        return new \PhpCsFixer\DocBlock\Line(\implode('', $line) . $dependsFunctionName);
    }
    /**
     * Helps to find where the function name in the doc block starts.
     */
    private function findWhereDependsFunctionNameStarts(array $line) : int
    {
        $counter = \count($line);
        do {
            --$counter;
        } while (' ' !== $line[$counter]);
        return $counter + 1;
    }
    /**
     * @param Line[] $lines
     *
     * @return Line[]
     */
    private function addTestAnnotation(array $lines, \PhpCsFixer\Tokenizer\Tokens $tokens, int $docBlockIndex) : array
    {
        $doc = new \PhpCsFixer\DocBlock\DocBlock($tokens[$docBlockIndex]->getContent());
        if (!$this->doesDocBlockContainTest($doc)) {
            $originalIndent = \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $docBlockIndex);
            $lineEnd = $this->whitespacesConfig->getLineEnding();
            \array_splice($lines, -1, 0, $originalIndent . ' *' . $lineEnd . $originalIndent . ' * @test' . $lineEnd);
        }
        return $lines;
    }
    private function doesDocBlockContainTest(\PhpCsFixer\DocBlock\DocBlock $doc) : bool
    {
        return 0 !== \count($doc->getAnnotationsOfType('test'));
    }
}
