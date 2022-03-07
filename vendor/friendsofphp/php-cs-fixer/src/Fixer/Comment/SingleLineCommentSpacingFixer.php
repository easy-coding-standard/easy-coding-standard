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
namespace PhpCsFixer\Fixer\Comment;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
final class SingleLineCommentSpacingFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Single-line comments must have proper spacing.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php
//comment 1
#comment 2
/*comment 3*/
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run after PhpdocToCommentFixer.
     */
    public function getPriority() : int
    {
        return 1;
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_COMMENT);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = \count($tokens) - 1; 0 <= $index; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_COMMENT)) {
                continue;
            }
            $content = $token->getContent();
            $contentLength = \strlen($content);
            if ('/' === $content[0]) {
                if ($contentLength < 3) {
                    continue;
                    // cheap check for "//"
                }
                if ('*' === $content[1]) {
                    // slash asterisk comment
                    if ($contentLength < 5 || '*' === $content[2] || \strpos($content, "\n") !== \false) {
                        continue;
                        // cheap check for "/**/", comment that looks like a PHPDoc, or multi line comment
                    }
                    $newContent = \rtrim(\substr($content, 0, -2)) . ' ' . \substr($content, -2);
                    $newContent = $this->fixCommentLeadingSpace($newContent, '/*');
                } else {
                    // double slash comment
                    $newContent = $this->fixCommentLeadingSpace($content, '//');
                }
            } else {
                // hash comment
                if ($contentLength < 2 || '[' === $content[1]) {
                    // cheap check for "#" or annotation (like) comment
                    continue;
                }
                $newContent = $this->fixCommentLeadingSpace($content, '#');
            }
            if ($newContent !== $content) {
                $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_COMMENT, $newContent]);
            }
        }
    }
    // fix space between comment open and leading text
    private function fixCommentLeadingSpace(string $content, string $prefix) : string
    {
        if (0 !== \PhpCsFixer\Preg::match(\sprintf('@^%s\\h+.*$@', \preg_quote($prefix, '@')), $content)) {
            return $content;
        }
        $position = \strlen($prefix);
        return \substr($content, 0, $position) . ' ' . \substr($content, $position);
    }
}
