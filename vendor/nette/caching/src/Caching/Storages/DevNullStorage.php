<?php

namespace Nette\Caching\Storages;

use Nette;


/**
 * Cache dummy storage.
 */
class DevNullStorage implements Nette\Caching\Storage
{
	use Nette\SmartObject;

	/**
	 * @param string $key
	 */
	public function read($key)
	{
	}


	/**
	 * @return void
	 * @param string $key
	 */
	public function lock($key)
	{
	}


	/**
	 * @return void
	 * @param string $key
	 */
	public function write($key, $data, array $dependencies)
	{
	}


	/**
	 * @return void
	 * @param string $key
	 */
	public function remove($key)
	{
	}


	/**
	 * @return void
	 */
	public function clean(array $conditions)
	{
	}
}
