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
namespace PhpCsFixer\Fixer\PhpTag;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Fixer\WhitespacesAwareFixerInterface;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * @author Ceeram <ceeram@cakephp.org>
 */
final class LinebreakAfterOpeningTagFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\WhitespacesAwareFixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function getDefinition() : \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Ensure there is no code on the same line as the PHP open tag.', [new \PhpCsFixer\FixerDefinition\CodeSample("<?php \$a = 1;\n\$b = 3;\n")]);
    }
    /**
     * {@inheritdoc}
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens) : bool
    {
        return $tokens->isTokenKindFound(\T_OPEN_TAG);
    }
    /**
     * {@inheritdoc}
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens) : void
    {
        // ignore files with short open tag and ignore non-monolithic files
        if (!$tokens[0]->isGivenKind(\T_OPEN_TAG) || !$tokens->isMonolithicPhp()) {
            return;
        }
        // ignore if linebreak already present
        if (\strpos($tokens[0]->getContent(), "\n") !== \false) {
            return;
        }
        $newlineFound = \false;
        foreach ($tokens as $token) {
            if ($token->isWhitespace() && \strpos($token->getContent(), "\n") !== \false) {
                $newlineFound = \true;
                break;
            }
        }
        // ignore one-line files
        if (!$newlineFound) {
            return;
        }
        $tokens[0] = new \PhpCsFixer\Tokenizer\Token([\T_OPEN_TAG, \rtrim($tokens[0]->getContent()) . $this->whitespacesConfig->getLineEnding()]);
    }
}
