<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210509\Symfony\Component\String;

if (!\function_exists(\ECSPrefix20210509\Symfony\Component\String\u::class)) {
    /**
     * @param string|null $string
     * @return \Symfony\Component\String\UnicodeString
     */
    function u($string = '')
    {
        return new \ECSPrefix20210509\Symfony\Component\String\UnicodeString(isset($string) ? $string : '');
    }
}
if (!\function_exists(\ECSPrefix20210509\Symfony\Component\String\b::class)) {
    /**
     * @param string|null $string
     * @return \Symfony\Component\String\ByteString
     */
    function b($string = '')
    {
        return new \ECSPrefix20210509\Symfony\Component\String\ByteString(isset($string) ? $string : '');
    }
}
if (!\function_exists(\ECSPrefix20210509\Symfony\Component\String\s::class)) {
    /**
     * @return \Symfony\Component\String\AbstractString
     * @param string|null $string
     */
    function s($string = '')
    {
        $string = isset($string) ? $string : '';
        return \preg_match('//u', $string) ? new \ECSPrefix20210509\Symfony\Component\String\UnicodeString($string) : new \ECSPrefix20210509\Symfony\Component\String\ByteString($string);
    }
}
