<?php

namespace Nette;

use Nette\Utils\ObjectHelpers;


/**
 * Strict class for better experience.
 * - 'did you mean' hints
 * - access to undeclared members throws exceptions
 * - support for @property annotations
 * - support for calling event handlers stored in $onEvent via onEvent()
 */
trait SmartObject
{
	/**
	 * @throws MemberAccessException
	 * @param string $name
	 */
	public function __call($name, array $args)
	{
		$name = (string) $name;
		$class = static::class;

		if (ObjectHelpers::hasProperty($class, $name) === 'event') { // calling event handlers
			$handlers = $this->$name !== null ? $this->$name : null;
			if (is_array($handlers) || $handlers instanceof \Traversable) {
				foreach ($handlers as $handler) {
					$handler(...$args);
				}
			} elseif ($handlers !== null) {
				throw new UnexpectedValueException("Property $class::$$name must be iterable or null, " . gettype($handlers) . ' given.');
			}

		} else {
			ObjectHelpers::strictCall($class, $name);
		}
	}


	/**
	 * @throws MemberAccessException
	 * @param string $name
	 */
	public static function __callStatic($name, array $args)
	{
		$name = (string) $name;
		ObjectHelpers::strictStaticCall(static::class, $name);
	}


	/**
	 * @return mixed
	 * @throws MemberAccessException if the property is not defined.
	 * @param string $name
	 */
	public function &__get($name)
	{
		$name = (string) $name;
		$class = static::class;

		if ($prop = isset(ObjectHelpers::getMagicProperties($class)[$name]) ? ObjectHelpers::getMagicProperties($class)[$name] : null) { // property getter
			if (!($prop & 0b0001)) {
				throw new MemberAccessException("Cannot read a write-only property $class::\$$name.");
			}
			$m = ($prop & 0b0010 ? 'get' : 'is') . $name;
			if ($prop & 0b0100) { // return by reference
				return $this->$m();
			} else {
				$val = $this->$m();
				return $val;
			}
		} else {
			ObjectHelpers::strictGet($class, $name);
		}
	}


	/**
	 * @param  mixed  $value
	 * @return void
	 * @throws MemberAccessException if the property is not defined or is read-only
	 * @param string $name
	 */
	public function __set($name, $value)
	{
		$name = (string) $name;
		$class = static::class;

		if (ObjectHelpers::hasProperty($class, $name)) { // unsetted property
			$this->$name = $value;

		} elseif ($prop = isset(ObjectHelpers::getMagicProperties($class)[$name]) ? ObjectHelpers::getMagicProperties($class)[$name] : null) { // property setter
			if (!($prop & 0b1000)) {
				throw new MemberAccessException("Cannot write to a read-only property $class::\$$name.");
			}
			$this->{'set' . $name}($value);

		} else {
			ObjectHelpers::strictSet($class, $name);
		}
	}


	/**
	 * @return void
	 * @throws MemberAccessException
	 * @param string $name
	 */
	public function __unset($name)
	{
		$name = (string) $name;
		$class = static::class;
		if (!ObjectHelpers::hasProperty($class, $name)) {
			throw new MemberAccessException("Cannot unset the property $class::\$$name.");
		}
	}


	/**
	 * @param string $name
	 * @return bool
	 */
	public function __isset($name)
	{
		$name = (string) $name;
		return isset(ObjectHelpers::getMagicProperties(static::class)[$name]);
	}
}
