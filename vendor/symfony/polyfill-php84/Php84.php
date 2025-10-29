<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Symfony\Polyfill\Php84;

/**
 * @author Ayesh Karunaratne <ayesh@aye.sh>
 * @author Pierre Ambroise <pierre27.ambroise@gmail.com>
 *
 * @internal
 */
final class Php84
{
    public static function mb_ucfirst(string $string, ?string $encoding = null) : string
    {
        if (null === $encoding) {
            $encoding = \mb_internal_encoding();
        }
        try {
            $validEncoding = @\mb_check_encoding('', $encoding);
        } catch (\ValueError $e) {
            throw new \ValueError(\sprintf('mb_ucfirst(): Argument #2 ($encoding) must be a valid encoding, "%s" given', $encoding));
        }
        // BC for PHP 7.3 and lower
        if (!$validEncoding) {
            throw new \ValueError(\sprintf('mb_ucfirst(): Argument #2 ($encoding) must be a valid encoding, "%s" given', $encoding));
        }
        $firstChar = \mb_substr($string, 0, 1, $encoding);
        $firstChar = \mb_convert_case($firstChar, \MB_CASE_TITLE, $encoding);
        return $firstChar . \mb_substr($string, 1, null, $encoding);
    }
    public static function mb_lcfirst(string $string, ?string $encoding = null) : string
    {
        if (null === $encoding) {
            $encoding = \mb_internal_encoding();
        }
        try {
            $validEncoding = @\mb_check_encoding('', $encoding);
        } catch (\ValueError $e) {
            throw new \ValueError(\sprintf('mb_lcfirst(): Argument #2 ($encoding) must be a valid encoding, "%s" given', $encoding));
        }
        // BC for PHP 7.3 and lower
        if (!$validEncoding) {
            throw new \ValueError(\sprintf('mb_lcfirst(): Argument #2 ($encoding) must be a valid encoding, "%s" given', $encoding));
        }
        $firstChar = \mb_substr($string, 0, 1, $encoding);
        $firstChar = \mb_convert_case($firstChar, \MB_CASE_LOWER, $encoding);
        return $firstChar . \mb_substr($string, 1, null, $encoding);
    }
    public static function array_find(array $array, callable $callback)
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $value;
            }
        }
        return null;
    }
    public static function array_find_key(array $array, callable $callback)
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return $key;
            }
        }
        return null;
    }
    public static function array_any(array $array, callable $callback) : bool
    {
        foreach ($array as $key => $value) {
            if ($callback($value, $key)) {
                return \true;
            }
        }
        return \false;
    }
    public static function array_all(array $array, callable $callback) : bool
    {
        foreach ($array as $key => $value) {
            if (!$callback($value, $key)) {
                return \false;
            }
        }
        return \true;
    }
    public static function fpow(float $num, float $exponent) : float
    {
        return $num ** $exponent;
    }
    public static function mb_trim(string $string, ?string $characters = null, ?string $encoding = null) : string
    {
        return self::mb_internal_trim('{^[%s]+|[%1$s]+$}Du', $string, $characters, $encoding, __FUNCTION__);
    }
    public static function mb_ltrim(string $string, ?string $characters = null, ?string $encoding = null) : string
    {
        return self::mb_internal_trim('{^[%s]+}Du', $string, $characters, $encoding, __FUNCTION__);
    }
    public static function mb_rtrim(string $string, ?string $characters = null, ?string $encoding = null) : string
    {
        return self::mb_internal_trim('{[%s]+$}Du', $string, $characters, $encoding, __FUNCTION__);
    }
    private static function mb_internal_trim(string $regex, string $string, ?string $characters, ?string $encoding, string $function) : string
    {
        if (null === $encoding) {
            $encoding = \mb_internal_encoding();
        }
        try {
            $validEncoding = @\mb_check_encoding('', $encoding);
        } catch (\ValueError $e) {
            throw new \ValueError(\sprintf('%s(): Argument #3 ($encoding) must be a valid encoding, "%s" given', $function, $encoding));
        }
        // BC for PHP 7.3 and lower
        if (!$validEncoding) {
            throw new \ValueError(\sprintf('%s(): Argument #3 ($encoding) must be a valid encoding, "%s" given', $function, $encoding));
        }
        if ('' === $characters) {
            return null === $encoding ? $string : \mb_convert_encoding($string, $encoding);
        }
        if ('UTF-8' === $encoding || \in_array(\strtolower($encoding), ['utf-8', 'utf8'], \true)) {
            $encoding = 'UTF-8';
        }
        $string = \mb_convert_encoding($string, 'UTF-8', $encoding);
        if (null !== $characters) {
            $characters = \mb_convert_encoding($characters, 'UTF-8', $encoding);
        }
        if (null === $characters) {
            $characters = "\\0 \f\n\r\t\v                 　᠎";
        } else {
            $characters = \preg_quote($characters);
        }
        $string = \preg_replace(\sprintf($regex, $characters), '', $string);
        if ('UTF-8' === $encoding) {
            return $string;
        }
        return \mb_convert_encoding($string, $encoding, 'UTF-8');
    }
    public static function grapheme_str_split(string $string, int $length)
    {
        if (0 > $length || 1073741823 < $length) {
            throw new \ValueError('grapheme_str_split(): Argument #2 ($length) must be greater than 0 and less than or equal to 1073741823.');
        }
        if ('' === $string) {
            return [];
        }
        $regex = ((float) \PCRE_VERSION < 10 ? (float) \PCRE_VERSION >= 8.32 : (float) \PCRE_VERSION >= 10.39) ? '\\X' : '(?:\\r\\n|(?:[ -~\\x{200C}\\x{200D}]|[ᆨ-ᇹ]+|[ᄀ-ᅟ]*(?:[가개갸걔거게겨계고과괘괴교구궈궤귀규그긔기까깨꺄꺠꺼께껴꼐꼬꽈꽤꾀꾜꾸꿔꿰뀌뀨끄끠끼나내냐냬너네녀녜노놔놰뇌뇨누눠눼뉘뉴느늬니다대댜댸더데뎌뎨도돠돼되됴두둬뒈뒤듀드듸디따때땨떄떠떼뗘뗴또똬뙈뙤뚀뚜뚸뛔뛰뜌뜨띄띠라래랴럐러레려례로롸뢔뢰료루뤄뤠뤼류르릐리마매먀먜머메며몌모뫄뫠뫼묘무뭐뭬뮈뮤므믜미바배뱌뱨버베벼볘보봐봬뵈뵤부붜붸뷔뷰브븨비빠빼뺘뺴뻐뻬뼈뼤뽀뽜뽸뾔뾰뿌뿨쀄쀠쀼쁘쁴삐사새샤섀서세셔셰소솨쇄쇠쇼수숴쉐쉬슈스싀시싸쌔쌰썌써쎄쎠쎼쏘쏴쐐쐬쑈쑤쒀쒜쒸쓔쓰씌씨아애야얘어에여예오와왜외요우워웨위유으의이자재쟈쟤저제져졔조좌좨죄죠주줘줴쥐쥬즈즤지짜째쨔쨰쩌쩨쪄쪠쪼쫘쫴쬐쬬쭈쭤쮀쮜쮸쯔쯰찌차채챠챼처체쳐쳬초촤쵀최쵸추춰췌취츄츠츼치카캐캬컈커케켜켸코콰쾌쾨쿄쿠쿼퀘퀴큐크킈키타태탸턔터테텨톄토톼퇘퇴툐투퉈퉤튀튜트틔티파패퍄퍠퍼페펴폐포퐈퐤푀표푸풔풰퓌퓨프픠피하해햐햬허헤혀혜호화홰회효후훠훼휘휴흐희히]?[ᅠ-ᆢ]+|[가-힣])[ᆨ-ᇹ]*|[ᄀ-ᅟ]+|[^\\p{Cc}\\p{Cf}\\p{Zl}\\p{Zp}])[\\p{Mn}\\p{Me}\\x{09BE}\\x{09D7}\\x{0B3E}\\x{0B57}\\x{0BBE}\\x{0BD7}\\x{0CC2}\\x{0CD5}\\x{0CD6}\\x{0D3E}\\x{0D57}\\x{0DCF}\\x{0DDF}\\x{200C}\\x{200D}\\x{1D165}\\x{1D16E}-\\x{1D172}]*|[\\p{Cc}\\p{Cf}\\p{Zl}\\p{Zp}])';
        if (!\preg_match_all('/' . $regex . '/u', $string, $matches)) {
            return \false;
        }
        if (1 === $length) {
            return $matches[0];
        }
        $chunks = \array_chunk($matches[0], $length);
        foreach ($chunks as &$chunk) {
            $chunk = \implode('', $chunk);
        }
        return $chunks;
    }
    public static function bcdivmod(string $num1, string $num2, ?int $scale = null) : ?array
    {
        if (null === ($quot = \bcdiv($num1, $num2, 0))) {
            return null;
        }
        $scale = $scale ?? (\PHP_VERSION_ID >= 70300 ? \bcscale() : (\ini_get('bcmath.scale') ?: 0));
        return [$quot, \bcmod($num1, $num2, $scale)];
    }
}
