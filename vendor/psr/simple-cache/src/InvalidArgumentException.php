<?php

namespace ECSPrefix20210508\Psr\SimpleCache;

/**
 * Exception interface for invalid cache arguments.
 *
 * When an invalid argument is passed it must throw an exception which implements
 * this interface
 */
interface InvalidArgumentException extends \ECSPrefix20210508\Psr\SimpleCache\CacheException
{
}
