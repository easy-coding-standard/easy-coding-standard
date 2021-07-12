<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210712\Symfony\Component\String;

if (!\function_exists(\ECSPrefix20210712\Symfony\Component\String\u::class)) {
    /**
     * @param string|null $string
     */
    function u($string = '') : \ECSPrefix20210712\Symfony\Component\String\UnicodeString
    {
        return new \ECSPrefix20210712\Symfony\Component\String\UnicodeString($string ?? '');
    }
}
if (!\function_exists(\ECSPrefix20210712\Symfony\Component\String\b::class)) {
    /**
     * @param string|null $string
     */
    function b($string = '') : \ECSPrefix20210712\Symfony\Component\String\ByteString
    {
        return new \ECSPrefix20210712\Symfony\Component\String\ByteString($string ?? '');
    }
}
if (!\function_exists(\ECSPrefix20210712\Symfony\Component\String\s::class)) {
    /**
     * @return UnicodeString|ByteString
     * @param string|null $string
     */
    function s($string = '') : \ECSPrefix20210712\Symfony\Component\String\AbstractString
    {
        $string = $string ?? '';
        return \preg_match('//u', $string) ? new \ECSPrefix20210712\Symfony\Component\String\UnicodeString($string) : new \ECSPrefix20210712\Symfony\Component\String\ByteString($string);
    }
}
