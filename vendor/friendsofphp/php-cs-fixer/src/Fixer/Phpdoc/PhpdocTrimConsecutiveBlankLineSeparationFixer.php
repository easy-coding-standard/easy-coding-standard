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
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
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
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     */
    public function getPriority() : int
    {
        return -41;
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
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
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
    private function fixSummary(\PhpCsFixer\DocBlock\DocBlock $doc, int $summaryEnd) : void
    {
        $nonBlankLineAfterSummary = $this->findNonBlankLine($doc, $summaryEnd);
        $this->removeExtraBlankLinesBetween($doc, $summaryEnd, $nonBlankLineAfterSummary);
    }
    private function fixDescription(\PhpCsFixer\DocBlock\DocBlock $doc, int $summaryEnd) : void
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
    private function fixAllTheRest(\PhpCsFixer\DocBlock\DocBlock $doc) : void
    {
        $annotationStart = $this->findFirstAnnotationOrEnd($doc);
        $lastLine = $this->reverseFindLastUsefulContent($doc, \count($doc->getLines()) - 1);
        if (null !== $lastLine && $annotationStart !== $lastLine) {
            $this->removeExtraBlankLinesBetween($doc, $annotationStart, $lastLine);
        }
    }
    private function removeExtraBlankLinesBetween(\PhpCsFixer\DocBlock\DocBlock $doc, int $from, int $to) : void
    {
        for ($index = $from + 1; $index < $to; ++$index) {
            $line = $doc->getLine($index);
            $next = $doc->getLine($index + 1);
            $this->removeExtraBlankLine($line, $next);
        }
    }
    private function removeExtraBlankLine(\PhpCsFixer\DocBlock\Line $current, \PhpCsFixer\DocBlock\Line $next) : void
    {
        if (!$current->isTheEnd() && !$current->containsUsefulContent() && !$next->isTheEnd() && !$next->containsUsefulContent()) {
            $current->remove();
        }
    }
    private function findNonBlankLine(\PhpCsFixer\DocBlock\DocBlock $doc, int $after) : ?int
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
    private function findFirstAnnotationOrEnd(\PhpCsFixer\DocBlock\DocBlock $doc) : int
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
    private function reverseFindLastUsefulContent(\PhpCsFixer\DocBlock\DocBlock $doc, int $from) : ?int
    {
        for ($index = $from - 1; $index >= 0; --$index) {
            if ($doc->getLine($index)->containsUsefulContent()) {
                return $index;
            }
        }
        return null;
    }
}
