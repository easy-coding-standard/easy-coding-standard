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
namespace PhpCsFixer\Fixer\StringNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Gregor Harlan
 */
final class NoTrailingWhitespaceInStringFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isAnyTokenKindsFound([\T_CONSTANT_ENCAPSED_STRING, \T_ENCAPSED_AND_WHITESPACE, \T_INLINE_HTML]);
    }
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
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('There must be no trailing whitespace in strings.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php \$a = '  \n    foo \n';\n")], null, 'Changing the whitespaces in strings might affect string comparisons and outputs.');
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        for ($index = $tokens->count() - 1, $last = \true; $index >= 0; --$index, $last = \false) {
            /** @var Token $token */
            $token = $tokens[$index];
            if (!$token->isGivenKind([\T_CONSTANT_ENCAPSED_STRING, \T_ENCAPSED_AND_WHITESPACE, \T_INLINE_HTML])) {
                continue;
            }
            $isInlineHtml = $token->isGivenKind(\T_INLINE_HTML);
            $regex = $isInlineHtml && $last ? '/\\h+(?=\\R|$)/' : '/\\h+(?=\\R)/';
            $content = \PhpCsFixer\Preg::replace($regex, '', $token->getContent());
            if ($token->getContent() === $content) {
                continue;
            }
            if (!$isInlineHtml || 0 === $index) {
                $this->updateContent($tokens, $index, $content);
                continue;
            }
            $prev = $index - 1;
            if ($tokens[$prev]->equals([\T_CLOSE_TAG, '?>']) && \PhpCsFixer\Preg::match('/^\\R/', $content, $match)) {
                $tokens[$prev] = new \PhpCsFixer\Tokenizer\Token([\T_CLOSE_TAG, $tokens[$prev]->getContent() . $match[0]]);
                $content = \substr($content, \strlen($match[0]));
                $content = \false === $content ? '' : $content;
                // @phpstan-ignore-line due to https://github.com/phpstan/phpstan/issues/1215 , awaiting PHP8 as min requirement of Fixer
            }
            $this->updateContent($tokens, $index, $content);
        }
    }
    private function updateContent(\PhpCsFixer\Tokenizer\Tokens $tokens, int $index, string $content) : void
    {
        if ('' === $content) {
            $tokens->clearAt($index);
            return;
        }
        $tokens[$index] = new \PhpCsFixer\Tokenizer\Token([$tokens[$index]->getId(), $content]);
    }
}
