<?php

namespace ECSPrefix20210516\Nette\Caching;

use ECSPrefix20210516\Nette;
/**
 * Implements the cache for a application.
 */
class Cache
{
    use Nette\SmartObject;
    /** dependency */
    const PRIORITY = 'priority', EXPIRATION = 'expire', EXPIRE = 'expire', SLIDING = 'sliding', TAGS = 'tags', FILES = 'files', ITEMS = 'items', CONSTS = 'consts', CALLBACKS = 'callbacks', NAMESPACES = 'namespaces', ALL = 'all';
    /** @internal */
    const NAMESPACE_SEPARATOR = "\0";
    /** @var Storage */
    private $storage;
    /** @var string */
    private $namespace;
    /**
     * @param string $namespace
     */
    public function __construct(\ECSPrefix20210516\Nette\Caching\Storage $storage, $namespace = null)
    {
        $this->storage = $storage;
        $this->namespace = $namespace . self::NAMESPACE_SEPARATOR;
    }
    /**
     * Returns cache storage.
     * @return \Nette\Caching\Storage
     */
    public final function getStorage()
    {
        return $this->storage;
    }
    /**
     * Returns cache namespace.
     * @return string
     */
    public final function getNamespace()
    {
        return (string) \substr($this->namespace, 0, -1);
    }
    /**
     * Returns new nested cache object.
     * @return static
     * @param string $namespace
     */
    public function derive($namespace)
    {
        $namespace = (string) $namespace;
        return new static($this->storage, $this->namespace . $namespace);
    }
    /**
     * Reads the specified item from the cache or generate it.
     * @param  mixed  $key
     * @return mixed
     */
    public function load($key, callable $generator = null)
    {
        $storageKey = $this->generateKey($key);
        $data = $this->storage->read($storageKey);
        if ($data === null && $generator) {
            $this->storage->lock($storageKey);
            try {
                $data = $generator(...[&$dependencies]);
            } catch (\Throwable $e) {
                $this->storage->remove($storageKey);
                throw $e;
            }
            $this->save($key, $data, $dependencies);
        }
        return $data;
    }
    /**
     * Reads multiple items from the cache.
     * @return mixed[]
     */
    public function bulkLoad(array $keys, callable $generator = null)
    {
        if (\count($keys) === 0) {
            return [];
        }
        foreach ($keys as $key) {
            if (!\is_scalar($key)) {
                throw new \ECSPrefix20210516\Nette\InvalidArgumentException('Only scalar keys are allowed in bulkLoad()');
            }
        }
        $result = [];
        if (!$this->storage instanceof \ECSPrefix20210516\Nette\Caching\BulkReader) {
            foreach ($keys as $key) {
                $result[$key] = $this->load($key, $generator ? function (&$dependencies) use($key, $generator) {
                    return $generator(...[$key, &$dependencies]);
                } : null);
            }
            return $result;
        }
        $storageKeys = \array_map([$this, 'generateKey'], $keys);
        $cacheData = $this->storage->bulkRead($storageKeys);
        foreach ($keys as $i => $key) {
            $storageKey = $storageKeys[$i];
            if (isset($cacheData[$storageKey])) {
                $result[$key] = $cacheData[$storageKey];
            } elseif ($generator) {
                $result[$key] = $this->load($key, function (&$dependencies) use($key, $generator) {
                    return $generator(...[$key, &$dependencies]);
                });
            } else {
                $result[$key] = null;
            }
        }
        return $result;
    }
    /**
     * Writes item into the cache.
     * Dependencies are:
     * - Cache::PRIORITY => (int) priority
     * - Cache::EXPIRATION => (timestamp) expiration
     * - Cache::SLIDING => (bool) use sliding expiration?
     * - Cache::TAGS => (array) tags
     * - Cache::FILES => (array|string) file names
     * - Cache::ITEMS => (array|string) cache items
     * - Cache::CONSTS => (array|string) cache items
     *
     * @param  mixed  $key
     * @param  mixed  $data
     * @return mixed  value itself
     * @throws Nette\InvalidArgumentException
     */
    public function save($key, $data, array $dependencies = null)
    {
        $key = $this->generateKey($key);
        if ($data instanceof \Closure) {
            $this->storage->lock($key);
            try {
                $data = $data(...[&$dependencies]);
            } catch (\Throwable $e) {
                $this->storage->remove($key);
                throw $e;
            }
        }
        if ($data === null) {
            $this->storage->remove($key);
        } else {
            $dependencies = $this->completeDependencies($dependencies);
            if (isset($dependencies[self::EXPIRATION]) && $dependencies[self::EXPIRATION] <= 0) {
                $this->storage->remove($key);
            } else {
                $this->storage->write($key, $data, $dependencies);
            }
            return $data;
        }
    }
    /**
     * @param mixed[]|null $dp
     * @return mixed[]
     */
    private function completeDependencies($dp)
    {
        // convert expire into relative amount of seconds
        if (isset($dp[self::EXPIRATION])) {
            $dp[self::EXPIRATION] = \ECSPrefix20210516\Nette\Utils\DateTime::from($dp[self::EXPIRATION])->format('U') - \time();
        }
        // make list from TAGS
        if (isset($dp[self::TAGS])) {
            $dp[self::TAGS] = \array_values((array) $dp[self::TAGS]);
        }
        // make list from NAMESPACES
        if (isset($dp[self::NAMESPACES])) {
            $dp[self::NAMESPACES] = \array_values((array) $dp[self::NAMESPACES]);
        }
        // convert FILES into CALLBACKS
        if (isset($dp[self::FILES])) {
            foreach (\array_unique((array) $dp[self::FILES]) as $item) {
                $dp[self::CALLBACKS][] = [[self::class, 'checkFile'], $item, @\filemtime($item) ?: null];
                // @ - stat may fail
            }
            unset($dp[self::FILES]);
        }
        // add namespaces to items
        if (isset($dp[self::ITEMS])) {
            $dp[self::ITEMS] = \array_unique(\array_map([$this, 'generateKey'], (array) $dp[self::ITEMS]));
        }
        // convert CONSTS into CALLBACKS
        if (isset($dp[self::CONSTS])) {
            foreach (\array_unique((array) $dp[self::CONSTS]) as $item) {
                $dp[self::CALLBACKS][] = [[self::class, 'checkConst'], $item, \constant($item)];
            }
            unset($dp[self::CONSTS]);
        }
        if (!\is_array($dp)) {
            $dp = [];
        }
        return $dp;
    }
    /**
     * Removes item from the cache.
     * @param  mixed  $key
     * @return void
     */
    public function remove($key)
    {
        $this->save($key, null);
    }
    /**
     * Removes items from the cache by conditions.
     * Conditions are:
     * - Cache::PRIORITY => (int) priority
     * - Cache::TAGS => (array) tags
     * - Cache::ALL => true
     * @return void
     */
    public function clean(array $conditions = null)
    {
        $conditions = (array) $conditions;
        if (isset($conditions[self::TAGS])) {
            $conditions[self::TAGS] = \array_values((array) $conditions[self::TAGS]);
        }
        $this->storage->clean($conditions);
    }
    /**
     * Caches results of function/method calls.
     * @return mixed
     */
    public function call(callable $function)
    {
        $key = \func_get_args();
        if (\is_array($function) && \is_object($function[0])) {
            $key[0][0] = \get_class($function[0]);
        }
        return $this->load($key, function () use($function, $key) {
            return $function(...\array_slice($key, 1));
        });
    }
    /**
     * Caches results of function/method calls.
     * @return \Closure
     */
    public function wrap(callable $function, array $dependencies = null)
    {
        return function () use($function, $dependencies) {
            $key = [$function, $args = \func_get_args()];
            if (\is_array($function) && \is_object($function[0])) {
                $key[0][0] = \get_class($function[0]);
            }
            return $this->load($key, function (&$deps) use($function, $args, $dependencies) {
                $deps = $dependencies;
                return $function(...$args);
            });
        };
    }
    /**
     * Starts the output cache.
     * @param  mixed  $key
     * @return \Nette\Caching\OutputHelper|null
     */
    public function capture($key)
    {
        $data = $this->load($key);
        if ($data === null) {
            return new \ECSPrefix20210516\Nette\Caching\OutputHelper($this, $key);
        }
        echo $data;
        return null;
    }
    /**
     * @deprecated  use capture()
     * @return \Nette\Caching\OutputHelper|null
     */
    public function start($key)
    {
        return $this->capture($key);
    }
    /**
     * Generates internal cache key.
     * @return string
     */
    protected function generateKey($key)
    {
        return $this->namespace . \md5(\is_scalar($key) ? (string) $key : \serialize($key));
    }
    /********************* dependency checkers ****************d*g**/
    /**
     * Checks CALLBACKS dependencies.
     * @return bool
     */
    public static function checkCallbacks(array $callbacks)
    {
        foreach ($callbacks as $callback) {
            if (!\array_shift($callback)(...$callback)) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * Checks CONSTS dependency.
     * @param string $const
     * @return bool
     */
    private static function checkConst($const, $value)
    {
        $const = (string) $const;
        return \defined($const) && \constant($const) === $value;
    }
    /**
     * Checks FILES dependency.
     * @param int|null $time
     * @param string $file
     * @return bool
     */
    private static function checkFile($file, $time)
    {
        $file = (string) $file;
        return @\filemtime($file) == $time;
        // @ - stat may fail
    }
}
