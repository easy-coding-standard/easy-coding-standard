<?php

namespace ECSPrefix20210514\Doctrine\Common\Annotations;

use ECSPrefix20210514\Psr\Cache\CacheItemPoolInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;
use Reflector;
use function array_map;
use function array_merge;
use function assert;
use function filemtime;
use function max;
use function rawurlencode;
use function time;
/**
 * A cache aware annotation reader.
 */
final class PsrCachedReader implements \ECSPrefix20210514\Doctrine\Common\Annotations\Reader
{
    /** @var Reader */
    private $delegate;
    /** @var CacheItemPoolInterface */
    private $cache;
    /** @var bool */
    private $debug;
    /** @var array<string, array<object>> */
    private $loadedAnnotations = [];
    /** @var int[] */
    private $loadedFilemtimes = [];
    /**
     * @param bool $debug
     */
    public function __construct(\ECSPrefix20210514\Doctrine\Common\Annotations\Reader $reader, \ECSPrefix20210514\Psr\Cache\CacheItemPoolInterface $cache, $debug = \false)
    {
        $debug = (bool) $debug;
        $this->delegate = $reader;
        $this->cache = $cache;
        $this->debug = (bool) $debug;
    }
    /**
     * {@inheritDoc}
     */
    public function getClassAnnotations(\ReflectionClass $class)
    {
        $cacheKey = $class->getName();
        if (isset($this->loadedAnnotations[$cacheKey])) {
            return $this->loadedAnnotations[$cacheKey];
        }
        $annots = $this->fetchFromCache($cacheKey, $class, 'getClassAnnotations', $class);
        return $this->loadedAnnotations[$cacheKey] = $annots;
    }
    /**
     * {@inheritDoc}
     */
    public function getClassAnnotation(\ReflectionClass $class, $annotationName)
    {
        foreach ($this->getClassAnnotations($class) as $annot) {
            if ($annot instanceof $annotationName) {
                return $annot;
            }
        }
        return null;
    }
    /**
     * {@inheritDoc}
     */
    public function getPropertyAnnotations(\ReflectionProperty $property)
    {
        $class = $property->getDeclaringClass();
        $cacheKey = $class->getName() . '$' . $property->getName();
        if (isset($this->loadedAnnotations[$cacheKey])) {
            return $this->loadedAnnotations[$cacheKey];
        }
        $annots = $this->fetchFromCache($cacheKey, $class, 'getPropertyAnnotations', $property);
        return $this->loadedAnnotations[$cacheKey] = $annots;
    }
    /**
     * {@inheritDoc}
     */
    public function getPropertyAnnotation(\ReflectionProperty $property, $annotationName)
    {
        foreach ($this->getPropertyAnnotations($property) as $annot) {
            if ($annot instanceof $annotationName) {
                return $annot;
            }
        }
        return null;
    }
    /**
     * {@inheritDoc}
     */
    public function getMethodAnnotations(\ReflectionMethod $method)
    {
        $class = $method->getDeclaringClass();
        $cacheKey = $class->getName() . '#' . $method->getName();
        if (isset($this->loadedAnnotations[$cacheKey])) {
            return $this->loadedAnnotations[$cacheKey];
        }
        $annots = $this->fetchFromCache($cacheKey, $class, 'getMethodAnnotations', $method);
        return $this->loadedAnnotations[$cacheKey] = $annots;
    }
    /**
     * {@inheritDoc}
     */
    public function getMethodAnnotation(\ReflectionMethod $method, $annotationName)
    {
        foreach ($this->getMethodAnnotations($method) as $annot) {
            if ($annot instanceof $annotationName) {
                return $annot;
            }
        }
        return null;
    }
    /**
     * @return void
     */
    public function clearLoadedAnnotations()
    {
        $this->loadedAnnotations = [];
        $this->loadedFilemtimes = [];
    }
    /**
     * @return mixed[]
     * @param string $cacheKey
     * @param string $method */
    private function fetchFromCache($cacheKey, \ReflectionClass $class, $method, \Reflector $reflector)
    {
        $cacheKey = (string) $cacheKey;
        $method = (string) $method;
        $cacheKey = \rawurlencode($cacheKey);
        $item = $this->cache->getItem($cacheKey);
        if (!$item->isHit() || $this->debug && !$this->refresh($cacheKey, $class)) {
            $this->cache->save($item->set($this->delegate->{$method}($reflector)));
        }
        return $item->get();
    }
    /**
     * Used in debug mode to check if the cache is fresh.
     *
     * @return bool Returns true if the cache was fresh, or false if the class
     * being read was modified since writing to the cache.
     * @param string $cacheKey
     */
    private function refresh($cacheKey, \ReflectionClass $class)
    {
        $cacheKey = (string) $cacheKey;
        $lastModification = $this->getLastModification($class);
        if ($lastModification === 0) {
            return \true;
        }
        $item = $this->cache->getItem('[C]' . $cacheKey);
        if ($item->isHit() && $item->get() >= $lastModification) {
            return \true;
        }
        $this->cache->save($item->set(\time()));
        return \false;
    }
    /**
     * Returns the time the class was last modified, testing traits and parents
     * @return int
     */
    private function getLastModification(\ReflectionClass $class)
    {
        $filename = $class->getFileName();
        if (isset($this->loadedFilemtimes[$filename])) {
            return $this->loadedFilemtimes[$filename];
        }
        $parent = $class->getParentClass();
        $lastModification = \max(\array_merge([$filename ? \filemtime($filename) : 0], \array_map(function (\ReflectionClass $reflectionTrait) : int {
            return $this->getTraitLastModificationTime($reflectionTrait);
        }, $class->getTraits()), \array_map(function (\ReflectionClass $class) : int {
            return $this->getLastModification($class);
        }, $class->getInterfaces()), $parent ? [$this->getLastModification($parent)] : []));
        \assert($lastModification !== \false);
        return $this->loadedFilemtimes[$filename] = $lastModification;
    }
    /**
     * @return int
     */
    private function getTraitLastModificationTime(\ReflectionClass $reflectionTrait)
    {
        $fileName = $reflectionTrait->getFileName();
        if (isset($this->loadedFilemtimes[$fileName])) {
            return $this->loadedFilemtimes[$fileName];
        }
        $lastModificationTime = \max(\array_merge([$fileName ? \filemtime($fileName) : 0], \array_map(function (\ReflectionClass $reflectionTrait) : int {
            return $this->getTraitLastModificationTime($reflectionTrait);
        }, $reflectionTrait->getTraits())));
        \assert($lastModificationTime !== \false);
        return $this->loadedFilemtimes[$fileName] = $lastModificationTime;
    }
}
