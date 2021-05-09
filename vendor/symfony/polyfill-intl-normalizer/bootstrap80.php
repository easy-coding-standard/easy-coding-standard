<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Polyfill\Intl\Normalizer as p;

if (!function_exists('normalizer_is_normalized')) {
    /**
     * @param string|null $string
     * @param int|null $form
     * @return bool
     */
    function normalizer_is_normalized($string, $form = p\Normalizer::FORM_C) { return p\Normalizer::isNormalized((string) $string, (int) $form); }
}
if (!function_exists('normalizer_normalize')) {
    /**
     * @return string|bool
     * @param string|null $string
     * @param int|null $form
     */
    function normalizer_normalize($string, $form = p\Normalizer::FORM_C) { return p\Normalizer::normalize((string) $string, (int) $form); }
}
