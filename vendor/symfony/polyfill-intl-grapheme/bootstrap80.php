<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Intl\Grapheme as p;

if (!defined('GRAPHEME_EXTR_COUNT')) {
    define('GRAPHEME_EXTR_COUNT', 0);
}
if (!defined('GRAPHEME_EXTR_MAXBYTES')) {
    define('GRAPHEME_EXTR_MAXBYTES', 1);
}
if (!defined('GRAPHEME_EXTR_MAXCHARS')) {
    define('GRAPHEME_EXTR_MAXCHARS', 2);
}

if (!function_exists('grapheme_extract')) {
    /**
     * @return string|bool
     * @param string|null $haystack
     * @param int|null $size
     * @param int|null $type
     * @param int|null $offset
     */
    function grapheme_extract($haystack, $size, $type = GRAPHEME_EXTR_COUNT, $offset = 0, &$next = null) { return p\Grapheme::grapheme_extract((string) $haystack, (int) $size, (int) $type, (int) $offset, $next); }
}
if (!function_exists('grapheme_stripos')) {
    /**
     * @return int|bool
     * @param string|null $haystack
     * @param string|null $needle
     * @param int|null $offset
     */
    function grapheme_stripos($haystack, $needle, $offset = 0) { return p\Grapheme::grapheme_stripos((string) $haystack, (string) $needle, (int) $offset); }
}
if (!function_exists('grapheme_stristr')) {
    /**
     * @return string|bool
     * @param string|null $haystack
     * @param string|null $needle
     * @param bool|null $beforeNeedle
     */
    function grapheme_stristr($haystack, $needle, $beforeNeedle = false) { return p\Grapheme::grapheme_stristr((string) $haystack, (string) $needle, (bool) $beforeNeedle); }
}
if (!function_exists('grapheme_strlen')) {
    /**
     * @return int|bool|null
     * @param string|null $string
     */
    function grapheme_strlen($string) { return p\Grapheme::grapheme_strlen((string) $string); }
}
if (!function_exists('grapheme_strpos')) {
    /**
     * @return int|bool
     * @param string|null $haystack
     * @param string|null $needle
     * @param int|null $offset
     */
    function grapheme_strpos($haystack, $needle, $offset = 0) { return p\Grapheme::grapheme_strpos((string) $haystack, (string) $needle, (int) $offset); }
}
if (!function_exists('grapheme_strripos')) {
    /**
     * @return int|bool
     * @param string|null $haystack
     * @param string|null $needle
     * @param int|null $offset
     */
    function grapheme_strripos($haystack, $needle, $offset = 0) { return p\Grapheme::grapheme_strripos((string) $haystack, (string) $needle, (int) $offset); }
}
if (!function_exists('grapheme_strrpos')) {
    /**
     * @return int|bool
     * @param string|null $haystack
     * @param string|null $needle
     * @param int|null $offset
     */
    function grapheme_strrpos($haystack, $needle, $offset = 0) { return p\Grapheme::grapheme_strrpos((string) $haystack, (string) $needle, (int) $offset); }
}
if (!function_exists('grapheme_strstr')) {
    /**
     * @return string|bool
     * @param string|null $haystack
     * @param string|null $needle
     * @param bool|null $beforeNeedle
     */
    function grapheme_strstr($haystack, $needle, $beforeNeedle = false) { return p\Grapheme::grapheme_strstr((string) $haystack, (string) $needle, (bool) $beforeNeedle); }
}
if (!function_exists('grapheme_substr')) {
    /**
     * @return string|bool
     * @param string|null $string
     * @param int|null $offset
     * @param int|null $length
     */
    function grapheme_substr($string, $offset, $length = null) { return p\Grapheme::grapheme_substr((string) $string, (int) $offset, (int) $length); }
}
