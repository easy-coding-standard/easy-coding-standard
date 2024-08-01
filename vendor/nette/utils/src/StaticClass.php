<?php

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */
declare (strict_types=1);
namespace ECSPrefix202408\Nette;

/**
 * Static class.
 */
trait StaticClass
{
    /**
     * Class is static and cannot be instantiated.
     */
    private function __construct()
    {
    }
    /**
     * Call to undefined static method.
     * @throws MemberAccessException
     * @return mixed
     */
    public static function __callStatic(string $name, array $args)
    {
        Utils\ObjectHelpers::strictStaticCall(static::class, $name);
    }
}
