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
namespace PhpCsFixer\Tokenizer\Transformer;

use PhpCsFixer\Tokenizer\AbstractTransformer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
/**
 * Transform NAME_QUALIFIED, T_NAME_FULLY_QUALIFIED and T_NAME_RELATIVE into T_NAMESPACE T_NS_SEPARATOR T_STRING.
 *
 * @author SpacePossum
 *
 * @internal
 */
final class NameQualifiedTransformer extends \PhpCsFixer\Tokenizer\AbstractTransformer
{
    /**
     * {@inheritdoc}
     * @return int
     */
    public function getPriority()
    {
        return 1;
        // must run before NamespaceOperatorTransformer
    }
    /**
     * {@inheritdoc}
     * @return int
     */
    public function getRequiredPhpVersionId()
    {
        return 80000;
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param int $index
     */
    public function process(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, $index)
    {
        $index = (int) $index;
        if ($token->isGivenKind([\T_NAME_QUALIFIED, \T_NAME_FULLY_QUALIFIED])) {
            $this->transformQualified($tokens, $token, $index);
        } elseif ($token->isGivenKind(\T_NAME_RELATIVE)) {
            $this->transformRelative($tokens, $token, $index);
        }
    }
    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function getCustomTokens()
    {
        return [];
    }
    /**
     * @return void
     * @param int $index
     */
    private function transformQualified(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, $index)
    {
        $index = (int) $index;
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
    /**
     * @return void
     * @param int $index
     */
    private function transformRelative(\PhpCsFixer\Tokenizer\Tokens $tokens, \PhpCsFixer\Tokenizer\Token $token, $index)
    {
        $index = (int) $index;
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
