<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Php80 as p;

if (\PHP_VERSION_ID >= 80000) {
    return;
}

if (!defined('FILTER_VALIDATE_BOOL') && defined('FILTER_VALIDATE_BOOLEAN')) {
    define('FILTER_VALIDATE_BOOL', \FILTER_VALIDATE_BOOLEAN);
}

if (!function_exists('fdiv')) {
    /**
     * @param float $num1
     * @param float $num2
     * @return float
     */
    function fdiv($num1, $num2) { return p\Php80::fdiv($num1, $num2); }
}
if (!function_exists('preg_last_error_msg')) {
    /**
     * @return string
     */
    function preg_last_error_msg() { return p\Php80::preg_last_error_msg(); }
}
if (!function_exists('str_contains')) {
    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_contains($haystack, $needle) { return p\Php80::str_contains($haystack, $needle); }
}
if (!function_exists('str_starts_with')) {
    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_starts_with($haystack, $needle) { return p\Php80::str_starts_with($haystack, $needle); }
}
if (!function_exists('str_ends_with')) {
    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     */
    function str_ends_with($haystack, $needle) { return p\Php80::str_ends_with($haystack, $needle); }
}
if (!function_exists('get_debug_type')) {
    /**
     * @return string
     */
    function get_debug_type($value) { return p\Php80::get_debug_type($value); }
}
if (!function_exists('get_resource_id')) {
    /**
     * @return int
     */
    function get_resource_id($resource) { return p\Php80::get_resource_id($resource); }
}
