<?php

namespace Nette;


/**
 * Static class.
 */
trait StaticClass
{
	/** @throws \Error */
	final public function __construct()
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
		$name = (string) $name;
		Utils\ObjectHelpers::strictStaticCall(static::class, $name);
	}
}
