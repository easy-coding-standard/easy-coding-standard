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
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Graham Campbell <graham@alt-three.com>
 */
final class NoBlankLinesAfterPhpdocFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        return $tokens->isTokenKindFound(\T_DOC_COMMENT);
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('There should not be blank lines between docblock and the documented element.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php

/**
 * This is the bar class.
 */


class Bar {}
')]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before HeaderCommentFixer, PhpdocAlignFixer, SingleBlankLineBeforeNamespaceFixer.
     * Must run after AlignMultilineCommentFixer, CommentToPhpdocFixer, PhpdocIndentFixer, PhpdocScalarFixer, PhpdocToCommentFixer, PhpdocTypesFixer.
     * @return int
     */
    public function getPriority()
    {
        return -20;
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        static $forbiddenSuccessors = [\T_DOC_COMMENT, \T_COMMENT, \T_WHITESPACE, \T_RETURN, \T_THROW, \T_GOTO, \T_CONTINUE, \T_BREAK, \T_DECLARE, \T_USE];
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(\T_DOC_COMMENT)) {
                continue;
            }
            // get the next non-whitespace token inc comments, provided
            // that there is whitespace between it and the current token
            $next = $tokens->getNextNonWhitespace($index);
            if ($index + 2 === $next && \false === $tokens[$next]->isGivenKind($forbiddenSuccessors)) {
                $this->fixWhitespace($tokens, $index + 1);
            }
        }
    }
    /**
     * Cleanup a whitespace token.
     * @return void
     * @param int $index
     */
    private function fixWhitespace(\PhpCsFixer\Tokenizer\Tokens $tokens, $index)
    {
        $content = $tokens[$index]->getContent();
        // if there is more than one new line in the whitespace, then we need to fix it
        if (\substr_count($content, "\n") > 1) {
            // the final bit of the whitespace must be the next statement's indentation
            $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, \substr($content, \strrpos($content, "\n"))]);
        }
    }
}
