<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ConfigTransformer20210601\Symfony\Component\String;

if (!\function_exists(\ConfigTransformer20210601\Symfony\Component\String\u::class)) {
    /**
     * @param string|null $string
     */
    function u($string = '') : \ConfigTransformer20210601\Symfony\Component\String\UnicodeString
    {
        return new \ConfigTransformer20210601\Symfony\Component\String\UnicodeString($string ?? '');
    }
}
if (!\function_exists(\ConfigTransformer20210601\Symfony\Component\String\b::class)) {
    /**
     * @param string|null $string
     */
    function b($string = '') : \ConfigTransformer20210601\Symfony\Component\String\ByteString
    {
        return new \ConfigTransformer20210601\Symfony\Component\String\ByteString($string ?? '');
    }
}
if (!\function_exists(\ConfigTransformer20210601\Symfony\Component\String\s::class)) {
    /**
     * @return UnicodeString|ByteString
     * @param string|null $string
     */
    function s($string = '') : \ConfigTransformer20210601\Symfony\Component\String\AbstractString
    {
        $string = $string ?? '';
        return \preg_match('//u', $string) ? new \ConfigTransformer20210601\Symfony\Component\String\UnicodeString($string) : new \ConfigTransformer20210601\Symfony\Component\String\ByteString($string);
    }
}
