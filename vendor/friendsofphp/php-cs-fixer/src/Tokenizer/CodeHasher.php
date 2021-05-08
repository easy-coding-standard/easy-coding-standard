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
namespace PhpCsFixer\Tokenizer;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class CodeHasher
{
    private function __construct()
    {
        // cannot create instance of util. class
    }
    /**
     * Calculate hash for code.
     * @param string $code
     */
    public static function calculateCodeHash($code) : string
    {
        if (\is_object($code)) {
            $code = (string) $code;
        }
        return (string) \crc32($code);
    }
}
