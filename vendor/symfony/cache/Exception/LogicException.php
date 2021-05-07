<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Cache\Exception;

use ECSPrefix20210507\Psr\Cache\CacheException as Psr6CacheInterface;
use ECSPrefix20210507\Psr\SimpleCache\CacheException as SimpleCacheInterface;
if (\interface_exists(\ECSPrefix20210507\Psr\SimpleCache\CacheException::class)) {
    class LogicException extends \LogicException implements \ECSPrefix20210507\Psr\Cache\CacheException, \ECSPrefix20210507\Psr\SimpleCache\CacheException
    {
    }
} else {
    class LogicException extends \LogicException implements \ECSPrefix20210507\Psr\Cache\CacheException
    {
    }
}
