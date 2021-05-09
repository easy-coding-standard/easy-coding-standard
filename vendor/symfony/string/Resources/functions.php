<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\String;

if (!\function_exists(u::class)) {
    /**
     * @param string|null $string
     * @return \Symfony\Component\String\UnicodeString
     */
    function u($string = '')
    {
        return new UnicodeString(isset($string) ? $string : '');
    }
}

if (!\function_exists(b::class)) {
    /**
     * @param string|null $string
     * @return \Symfony\Component\String\ByteString
     */
    function b($string = '')
    {
        return new ByteString(isset($string) ? $string : '');
    }
}

if (!\function_exists(s::class)) {
    /**
     * @return \Symfony\Component\String\AbstractString
     * @param string|null $string
     */
    function s($string = '')
    {
        $string = isset($string) ? $string : '';

        return preg_match('//u', $string) ? new UnicodeString($string) : new ByteString($string);
    }
}
