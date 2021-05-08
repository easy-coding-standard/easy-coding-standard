<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Cache\Exception;

use ECSPrefix20210508\Psr\Cache\InvalidArgumentException as Psr6CacheInterface;
use ECSPrefix20210508\Psr\SimpleCache\InvalidArgumentException as SimpleCacheInterface;
if (\interface_exists(\ECSPrefix20210508\Psr\SimpleCache\InvalidArgumentException::class)) {
    class InvalidArgumentException extends \InvalidArgumentException implements \ECSPrefix20210508\Psr\Cache\InvalidArgumentException, \ECSPrefix20210508\Psr\SimpleCache\InvalidArgumentException
    {
    }
} else {
    class InvalidArgumentException extends \InvalidArgumentException implements \ECSPrefix20210508\Psr\Cache\InvalidArgumentException
    {
    }
}
