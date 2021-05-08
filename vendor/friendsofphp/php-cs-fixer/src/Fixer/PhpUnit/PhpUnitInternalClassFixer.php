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
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerConfiguration\AllowedValueSubset;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Gert de Pagter <BackEndTea@gmail.com>
 */
final class PhpUnitInternalClassFixer extends \PhpCsFixer\Fixer\AbstractPhpUnitFixer implements \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface, \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('All PHPUnit test classes should be marked as internal.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nclass MyTest extends TestCase {}\n"), new \PhpCsFixer\FixerDefinition\CodeSample("<?php\nclass MyTest extends TestCase {}\nfinal class FinalTest extends TestCase {}\nabstract class AbstractTest extends TestCase {}\n", ['types' => ['final']])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before FinalInternalClassFixer.
     * @return int
     */
    public function getPriority()
    {
        return 68;
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
     */
    protected function createConfigurationDefinition()
    {
        $types = ['normal', 'final', 'abstract'];
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('types', 'What types of classes to mark as internal'))->setAllowedValues([new \PhpCsFixer\FixerConfiguration\AllowedValueSubset($types)])->setAllowedTypes(['array'])->setDefault(['normal', 'final'])->getOption()]);
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param int $startIndex
     * @param int $endIndex
     */
    protected function applyPhpUnitClassFix(\PhpCsFixer\Tokenizer\Tokens $tokens, $startIndex, $endIndex)
    {
        $classIndex = $tokens->getPrevTokenOfKind($startIndex, [[\T_CLASS]]);
        if (!$this->isAllowedByConfiguration($tokens, $classIndex)) {
            return;
        }
        $docBlockIndex = $this->getDocBlockIndex($tokens, $classIndex);
        if ($this->isPHPDoc($tokens, $docBlockIndex)) {
            $this->updateDocBlockIfNeeded($tokens, $docBlockIndex);
        } else {
            $this->createDocBlock($tokens, $docBlockIndex);
        }
    }
    /**
     * @param int $i
     * @return bool
     */
    private function isAllowedByConfiguration(\PhpCsFixer\Tokenizer\Tokens $tokens, $i)
    {
        $typeIndex = $tokens->getPrevMeaningfulToken($i);
        if ($tokens[$typeIndex]->isGivenKind(\T_FINAL)) {
            return \in_array('final', $this->configuration['types'], \true);
        }
        if ($tokens[$typeIndex]->isGivenKind(\T_ABSTRACT)) {
            return \in_array('abstract', $this->configuration['types'], \true);
        }
        return \in_array('normal', $this->configuration['types'], \true);
    }
    /**
     * @return void
     * @param int $docBlockIndex
     */
    private function createDocBlock(\PhpCsFixer\Tokenizer\Tokens $tokens, $docBlockIndex)
    {
        $lineEnd = $this->whitespacesConfig->getLineEnding();
        $originalIndent = \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $tokens->getNextNonWhitespace($docBlockIndex));
        $toInsert = [new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, '/**' . $lineEnd . "{$originalIndent} * @internal" . $lineEnd . "{$originalIndent} */"]), new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $lineEnd . $originalIndent])];
        $index = $tokens->getNextMeaningfulToken($docBlockIndex);
        $tokens->insertAt($index, $toInsert);
    }
    /**
     * @return void
     * @param int $docBlockIndex
     */
    private function updateDocBlockIfNeeded(\PhpCsFixer\Tokenizer\Tokens $tokens, $docBlockIndex)
    {
        $doc = new \PhpCsFixer\DocBlock\DocBlock($tokens[$docBlockIndex]->getContent());
        if (!empty($doc->getAnnotationsOfType('internal'))) {
            return;
        }
        $doc = $this->makeDocBlockMultiLineIfNeeded($doc, $tokens, $docBlockIndex);
        $lines = $this->addInternalAnnotation($doc, $tokens, $docBlockIndex);
        $lines = \implode('', $lines);
        $tokens[$docBlockIndex] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $lines]);
    }
    /**
     * @return mixed[]
     * @param int $docBlockIndex
     */
    private function addInternalAnnotation(\PhpCsFixer\DocBlock\DocBlock $docBlock, \PhpCsFixer\Tokenizer\Tokens $tokens, $docBlockIndex)
    {
        $lines = $docBlock->getLines();
        $originalIndent = \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $docBlockIndex);
        $lineEnd = $this->whitespacesConfig->getLineEnding();
        \array_splice($lines, -1, 0, $originalIndent . ' *' . $lineEnd . $originalIndent . ' * @internal' . $lineEnd);
        return $lines;
    }
    /**
     * @param int $docBlockIndex
     * @return \PhpCsFixer\DocBlock\DocBlock
     */
    private function makeDocBlockMultiLineIfNeeded(\PhpCsFixer\DocBlock\DocBlock $doc, \PhpCsFixer\Tokenizer\Tokens $tokens, $docBlockIndex)
    {
        $lines = $doc->getLines();
        if (1 === \count($lines) && empty($doc->getAnnotationsOfType('internal'))) {
            $indent = \PhpCsFixer\Tokenizer\Analyzer\WhitespacesAnalyzer::detectIndent($tokens, $tokens->getNextNonWhitespace($docBlockIndex));
            $doc->makeMultiLine($indent, $this->whitespacesConfig->getLineEnding());
            return $doc;
        }
        return $doc;
    }
}
