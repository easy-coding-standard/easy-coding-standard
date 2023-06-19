<?php

namespace ECSPrefix202306;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use ECSPrefix202306\Symfony\Component\VarDumper\Caster\ScalarStub;
use ECSPrefix202306\Symfony\Component\VarDumper\VarDumper;
if (!\function_exists('ECSPrefix202306\\dump')) {
    /**
     * @author Nicolas Grekas <p@tchwork.com>
     * @author Alexandre Daubois <alex.daubois@gmail.com>
     * @param mixed ...$vars
     * @return mixed
     */
    function dump(...$vars)
    {
        if (!$vars) {
            VarDumper::dump(new ScalarStub('🐛'));
            return null;
        }
        if (\array_key_exists(0, $vars) && 1 === \count($vars)) {
            VarDumper::dump($vars[0]);
            $k = 0;
        } else {
            foreach ($vars as $k => $v) {
                VarDumper::dump($v, \is_int($k) ? 1 + $k : $k);
            }
        }
        if (1 < \count($vars)) {
            return $vars;
        }
        return $vars[$k];
    }
}
if (!\function_exists('ECSPrefix202306\\dd')) {
    /**
     * @return never
     * @param mixed ...$vars
     */
    function dd(...$vars)
    {
        if (!\in_array(\PHP_SAPI, ['cli', 'phpdbg'], \true) && !\headers_sent()) {
            \header('HTTP/1.1 500 Internal Server Error');
        }
        if (\array_key_exists(0, $vars) && 1 === \count($vars)) {
            VarDumper::dump($vars[0]);
        } else {
            foreach ($vars as $k => $v) {
                VarDumper::dump($v, \is_int($k) ? 1 + $k : $k);
            }
        }
        exit(1);
    }
}
