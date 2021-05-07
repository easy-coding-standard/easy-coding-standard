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
namespace PhpCsFixer\Fixer\Whitespace;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * Fixer for rules defined in PSR2 ¶2.4.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class IndentationTypeFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * @var string
     */
    private $indent;
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Code MUST use configured indentation type.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php\n\nif (true) {\n\techo 'Hello!';\n}\n")]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocIndentFixer.
     * Must run after ClassAttributesSeparationFixer.
     * @return int
     */
    public function getPriority()
    {
        return 50;
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    public function isCandidate($tokens)
    {
        return $tokens->isAnyTokenKindsFound([\T_COMMENT, \T_DOC_COMMENT, \T_WHITESPACE]);
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param \SplFileInfo $file
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    protected function applyFix($file, $tokens)
    {
        $this->indent = $this->whitespacesConfig->getIndent();
        foreach ($tokens as $index => $token) {
            if ($token->isComment()) {
                $tokens[$index] = $this->fixIndentInComment($tokens, $index);
                continue;
            }
            if ($token->isWhitespace()) {
                $tokens[$index] = $this->fixIndentToken($tokens, $index);
                continue;
            }
        }
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @return \PhpCsFixer\Tokenizer\Token
     */
    private function fixIndentInComment($tokens, $index)
    {
        $content = \PhpCsFixer\Preg::replace('/^(?:(?<! ) {1,3})?\\t/m', '\\1    ', $tokens[$index]->getContent(), -1, $count);
        // Also check for more tabs.
        while (0 !== $count) {
            $content = \PhpCsFixer\Preg::replace('/^(\\ +)?\\t/m', '\\1    ', $content, -1, $count);
        }
        $indent = $this->indent;
        // change indent to expected one
        $content = \PhpCsFixer\Preg::replaceCallback('/^(?:    )+/m', function (array $matches) use($indent) {
            return $this->getExpectedIndent($matches[0], $indent);
        }, $content);
        return new \PhpCsFixer\Tokenizer\Token([$tokens[$index]->getId(), $content]);
    }
    /**
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @param int $index
     * @return \PhpCsFixer\Tokenizer\Token
     */
    private function fixIndentToken($tokens, $index)
    {
        $content = $tokens[$index]->getContent();
        $previousTokenHasTrailingLinebreak = \false;
        // @TODO this can be removed when we have a transformer for "T_OPEN_TAG" to "T_OPEN_TAG + T_WHITESPACE"
        if (\false !== \strpos($tokens[$index - 1]->getContent(), "\n")) {
            $content = "\n" . $content;
            $previousTokenHasTrailingLinebreak = \true;
        }
        $indent = $this->indent;
        $newContent = \PhpCsFixer\Preg::replaceCallback(
            '/(\\R)(\\h+)/',
            // find indent
            function (array $matches) use($indent) {
                // normalize mixed indent
                $content = \PhpCsFixer\Preg::replace('/(?:(?<! ) {1,3})?\\t/', '    ', $matches[2]);
                // change indent to expected one
                return $matches[1] . $this->getExpectedIndent($content, $indent);
            },
            $content
        );
        if ($previousTokenHasTrailingLinebreak) {
            $newContent = \substr($newContent, 1);
        }
        return new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, $newContent]);
    }
    /**
     * @return string mixed
     * @param string $content
     * @param string $indent
     */
    private function getExpectedIndent($content, $indent)
    {
        if ("\t" === $indent) {
            $content = \str_replace('    ', $indent, $content);
        }
        return $content;
    }
}
