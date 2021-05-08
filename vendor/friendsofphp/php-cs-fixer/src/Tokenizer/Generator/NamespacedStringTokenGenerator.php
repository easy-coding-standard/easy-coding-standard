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
namespace PhpCsFixer\Tokenizer\Generator;

use PhpCsFixer\Tokenizer\Token;
/**
 * @internal
 */
final class NamespacedStringTokenGenerator
{
    /**
     * Parse a string that contains a namespace into tokens.
     *
     * @return Token[]
     * @param string $input
     */
    public function generate($input) : array
    {
        if (\is_object($input)) {
            $input = (string) $input;
        }
        $tokens = [];
        $parts = \explode('\\', $input);
        foreach ($parts as $index => $part) {
            $tokens[] = new \PhpCsFixer\Tokenizer\Token([\T_STRING, $part]);
            if ($index !== \count($parts) - 1) {
                $tokens[] = new \PhpCsFixer\Tokenizer\Token([\T_NS_SEPARATOR, '\\']);
            }
        }
        return $tokens;
    }
}
