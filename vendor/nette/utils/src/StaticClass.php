<?php

namespace ECSPrefix20210507\Nette;

/**
 * Static class.
 */
trait StaticClass
{
    /** @throws \Error */
    public final function __construct()
    {
        throw new \Error('Class ' . static::class . ' is static and cannot be instantiated.');
    }
    /**
     * Call to undefined static method.
     * @return void
     * @throws MemberAccessException
     * @param string $name
     */
    public static function __callStatic($name, array $args)
    {
        \ECSPrefix20210507\Nette\Utils\ObjectHelpers::strictStaticCall(static::class, $name);
    }
}
