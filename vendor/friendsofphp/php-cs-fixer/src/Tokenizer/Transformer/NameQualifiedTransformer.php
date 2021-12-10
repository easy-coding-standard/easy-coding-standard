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
namespace PhpCsFixer\Tokenizer\Transformer;

use PhpCsFixer\Tokenizer\AbstractTransformer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * Transform NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED and T_NAME_RELATIVE into T_NAMESPACE T_NS_SEPARATOR T_STRING.
 *
 * @internal
 */
final class NameQualifiedTransformer extends \PhpCsFixer\Tokenizer\AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function getPriority() : int
    {
        return 1;
        // must run before NamespaceOperatorTransformer
    }
    /**
     * {@inheritdoc}
     */
    public function getRequiredPhpVersionId() : int
    {
        return 80000;
    }
    /**
     * {@inheritdoc}
     */
    public function process(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, int $index) : void
    {
        if ($token->isGivenKind([\T_NAME_QUALIFIED, \T_NAME_FULLY_QUALIFIED])) {
            $this->transformQualified($tokens, $token, $index);
        } elseif ($token->isGivenKind(\T_NAME_RELATIVE)) {
            $this->transformRelative($tokens, $token, $index);
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getCustomTokens() : array
    {
        return [];
    }
    private function transformQualified(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, int $index) : void
    {
        $parts = \explode('\\', $token->getContent());
        $newTokens = [];
        if ('' === $parts[0]) {
            $newTokens[] = new \PhpCsFixer\Tokenizer\Token([\T_NS_SEPARATOR, '\\']);
            \array_shift($parts);
        }
        foreach ($parts as $part) {
            $newTokens[] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, $part]);
            $newTokens[] = new \PhpCsFixer\Tokenizer\Token([\T_NS_SEPARATOR, '\\']);
        }
        \array_pop($newTokens);
        $tokens->overrideRange($index, $index, $newTokens);
    }
    private function transformRelative(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, int $index) : void
    {
        $parts = \explode('\\', $token->getContent());
        $newTokens = [new \PhpCsFixer\Tokenizer\Token([\T_NAMESPACE, \array_shift($parts)]), new \PhpCsFixer\Tokenizer\Token([\T_NS_SEPARATOR, '\\'])];
        foreach ($parts as $part) {
            $newTokens[] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, $part]);
            $newTokens[] = new \PhpCsFixer\Tokenizer\Token([\T_NS_SEPARATOR, '\\']);
        }
        \array_pop($newTokens);
        $tokens->overrideRange($index, $index, $newTokens);
    }
}
