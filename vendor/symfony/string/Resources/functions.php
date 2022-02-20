<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20220220\Symfony\Component\String;

if (!\function_exists(\ECSPrefix20220220\Symfony\Component\String\u::class)) {
    function u(?string $string = '') : \ECSPrefix20220220\Symfony\Component\String\UnicodeString
    {
        return new \ECSPrefix20220220\Symfony\Component\String\UnicodeString($string ?? '');
    }
}
if (!\function_exists(\ECSPrefix20220220\Symfony\Component\String\b::class)) {
    function b(?string $string = '') : \ECSPrefix20220220\Symfony\Component\String\ByteString
    {
        return new \ECSPrefix20220220\Symfony\Component\String\ByteString($string ?? '');
    }
}
if (!\function_exists(\ECSPrefix20220220\Symfony\Component\String\s::class)) {
    /**
     * @return UnicodeString|ByteString
     */
    function s(?string $string = '') : \ECSPrefix20220220\Symfony\Component\String\AbstractString
    {
        $string = $string ?? '';
        return \preg_match('//u', $string) ? new \ECSPrefix20220220\Symfony\Component\String\UnicodeString($string) : new \ECSPrefix20220220\Symfony\Component\String\ByteString($string);
    }
}
