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
namespace PhpCsFixer\Fixer\Phpdoc;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\DocBlock\Line;
use PhpCsFixer\DocBlock\ShortDescription;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Nobu Funaki <nobu.funaki@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class PhpdocTrimConsecutiveBlankLineSeparationFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Removes extra blank lines after summary and after description in PHPDoc.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
/**
 * Summary.
 *
 *
 * Description that contain 4 lines,
 *
 *
 * while 2 of them are blank!
 *
 *
 * @param string $foo
 *
 *
 * @dataProvider provideFixCases
 */
function fnc($foo) {}
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocAlignFixer.
     * Must run after AlignMultilineCommentFixer, AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     * @return int
     */
    public function getPriority()
    {
        return -41;
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    public function isCandidate($tokens)
    {
        return $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param \SplFileInfo $file
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    protected function applyFix($file, $tokens)
    {
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            $doc = new \PhpCsFixer\DocBlock\DocBlock($token->getContent());
            $summaryEnd = (new \PhpCsFixer\DocBlock\ShortDescription($doc))->getEnd();
            if (null !== $summaryEnd) {
                $this->fixSummary($doc, $summaryEnd);
                $this->fixDescription($doc, $summaryEnd);
            }
            $this->fixAllTheRest($doc);
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_DOC_COMMENT, $doc->getContent()]);
        }
    }
    /**
     * @return void
     * @param \PhpCsFixer\DocBlock\DocBlock $doc
     * @param int $summaryEnd
     */
    private function fixSummary($doc, $summaryEnd)
    {
        $nonBlankLineAfterSummary = $this->findNonBlankLine($doc, $summaryEnd);
        $this->removeExtraBlankLinesBetween($doc, $summaryEnd, $nonBlankLineAfterSummary);
    }
    /**
     * @return void
     * @param \PhpCsFixer\DocBlock\DocBlock $doc
     * @param int $summaryEnd
     */
    private function fixDescription($doc, $summaryEnd)
    {
        $annotationStart = $this->findFirstAnnotationOrEnd($doc);
        // assuming the end of the Description appears before the first Annotation
        $descriptionEnd = $this->reverseFindLastUsefulContent($doc, $annotationStart);
        if (null === $descriptionEnd || $summaryEnd === $descriptionEnd) {
            return;
            // no Description
        }
        if ($annotationStart === \count($doc->getLines()) - 1) {
            return;
            // no content after Description
        }
        $this->removeExtraBlankLinesBetween($doc, $descriptionEnd, $annotationStart);
    }
    /**
     * @return void
     * @param \PhpCsFixer\DocBlock\DocBlock $doc
     */
    private function fixAllTheRest($doc)
    {
        $annotationStart = $this->findFirstAnnotationOrEnd($doc);
        $lastLine = $this->reverseFindLastUsefulContent($doc, \count($doc->getLines()) - 1);
        if (null !== $lastLine && $annotationStart !== $lastLine) {
            $this->removeExtraBlankLinesBetween($doc, $annotationStart, $lastLine);
        }
    }
    /**
     * @return void
     * @param \PhpCsFixer\DocBlock\DocBlock $doc
     * @param int $from
     * @param int $to
     */
    private function removeExtraBlankLinesBetween($doc, $from, $to)
    {
        for ($index = $from + 1; $index < $to; ++$index) {
            $line = $doc->getLine($index);
            $next = $doc->getLine($index + 1);
            $this->removeExtraBlankLine($line, $next);
        }
    }
    /**
     * @return void
     * @param \PhpCsFixer\DocBlock\Line $current
     * @param \PhpCsFixer\DocBlock\Line $next
     */
    private function removeExtraBlankLine($current, $next)
    {
        if (!$current->isTheEnd() && !$current->containsUsefulContent() && !$next->isTheEnd() && !$next->containsUsefulContent()) {
            $current->remove();
        }
    }
    /**
     * @return int|null
     * @param \PhpCsFixer\DocBlock\DocBlock $doc
     * @param int $after
     */
    private function findNonBlankLine($doc, $after)
    {
        foreach ($doc->getLines() as $index => $line) {
            if ($index <= $after) {
                continue;
            }
            if ($line->containsATag() || $line->containsUsefulContent() || $line->isTheEnd()) {
                return $index;
            }
        }
        return null;
    }
    /**
     * @param \PhpCsFixer\DocBlock\DocBlock $doc
     * @return int
     */
    private function findFirstAnnotationOrEnd($doc)
    {
        $index = null;
        foreach ($doc->getLines() as $index => $line) {
            if ($line->containsATag()) {
                return $index;
            }
        }
        return $index;
        // no Annotation, return the last line
    }
    /**
     * @return int|null
     * @param \PhpCsFixer\DocBlock\DocBlock $doc
     * @param int $from
     */
    private function reverseFindLastUsefulContent($doc, $from)
    {
        for ($index = $from - 1; $index >= 0; --$index) {
            if ($doc->getLine($index)->containsUsefulContent()) {
                return $index;
            }
        }
        return null;
    }
}
