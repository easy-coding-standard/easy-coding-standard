<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210509\Symfony\Component\VarExporter\Internal;

use ECSPrefix20210509\Symfony\Component\VarExporter\Exception\ClassNotFoundException;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
class Hydrator
{
    public static $hydrators = [];
    public $registry;
    public $values;
    public $properties;
    public $value;
    public $wakeups;
    /**
     * @param \Symfony\Component\VarExporter\Internal\Registry|null $registry
     * @param \Symfony\Component\VarExporter\Internal\Values|null $values
     */
    public function __construct($registry, $values, array $properties, $value, array $wakeups)
    {
        $this->registry = $registry;
        $this->values = $values;
        $this->properties = $properties;
        $this->value = $value;
        $this->wakeups = $wakeups;
    }
    public static function hydrate($objects, $values, $properties, $value, $wakeups)
    {
        foreach ($properties as $class => $vars) {
            (isset(self::$hydrators[$class]) ? self::$hydrators[$class] : self::getHydrator($class))($vars, $objects);
        }
        foreach ($wakeups as $k => $v) {
            if (\is_array($v)) {
                $objects[\strlen($objects) - $k]->__unserialize($v);
            } else {
                $objects[$v]->__wakeup();
            }
        }
        return $value;
    }
    public static function getHydrator($class)
    {
        if ('stdClass' === $class) {
            return self::$hydrators[$class] = static function ($properties, $objects) {
                foreach ($properties as $name => $values) {
                    foreach ($values as $i => $v) {
                        $objects[$i]->{$name} = $v;
                    }
                }
            };
        }
        if (!\class_exists($class) && !\interface_exists($class, \false) && !\trait_exists($class, \false)) {
            throw new \ECSPrefix20210509\Symfony\Component\VarExporter\Exception\ClassNotFoundException($class);
        }
        $classReflector = new \ReflectionClass($class);
        if (!$classReflector->isInternal()) {
            return self::$hydrators[$class] = (isset(self::$hydrators['stdClass']) ? self::$hydrators['stdClass'] : self::getHydrator('stdClass'))->bindTo(null, $class);
        }
        if ($classReflector->name !== $class) {
            return isset(self::$hydrators[$classReflector->name]) ? self::$hydrators[$classReflector->name] : self::getHydrator($classReflector->name);
        }
        switch ($class) {
            case 'ArrayIterator':
            case 'ArrayObject':
                $constructor = \Closure::fromCallable([$classReflector->getConstructor(), 'invokeArgs']);
                return self::$hydrators[$class] = static function ($properties, $objects) use($constructor) {
                    foreach ($properties as $name => $values) {
                        if ("\0" !== $name) {
                            foreach ($values as $i => $v) {
                                $objects[$i]->{$name} = $v;
                            }
                        }
                    }
                    foreach (isset($properties["\0"]) ? $properties["\0"] : [] as $i => $v) {
                        $constructor($objects[$i], $v);
                    }
                };
            case 'ErrorException':
                return self::$hydrators[$class] = (isset(self::$hydrators['stdClass']) ? self::$hydrators['stdClass'] : self::getHydrator('stdClass'))->bindTo(null, new \ECSPrefix20210509\Symfony\Component\VarExporter\Internal\Anonymous__b3dd2147f1529ab4e7eddc492e2855ce__0());
            case 'TypeError':
                return self::$hydrators[$class] = (isset(self::$hydrators['stdClass']) ? self::$hydrators['stdClass'] : self::getHydrator('stdClass'))->bindTo(null, new \ECSPrefix20210509\Symfony\Component\VarExporter\Internal\Anonymous__b3dd2147f1529ab4e7eddc492e2855ce__1());
            case 'SplObjectStorage':
                return self::$hydrators[$class] = static function ($properties, $objects) {
                    foreach ($properties as $name => $values) {
                        if ("\0" === $name) {
                            foreach ($values as $i => $v) {
                                for ($j = 0; $j < \count($v); ++$j) {
                                    $objects[$i]->attach($v[$j], $v[++$j]);
                                }
                            }
                            continue;
                        }
                        foreach ($values as $i => $v) {
                            $objects[$i]->{$name} = $v;
                        }
                    }
                };
        }
        $propertySetters = [];
        foreach ($classReflector->getProperties() as $propertyReflector) {
            if (!$propertyReflector->isStatic()) {
                $propertyReflector->setAccessible(\true);
                $propertySetters[$propertyReflector->name] = \Closure::fromCallable([$propertyReflector, 'setValue']);
            }
        }
        if (!$propertySetters) {
            return self::$hydrators[$class] = isset(self::$hydrators['stdClass']) ? self::$hydrators['stdClass'] : self::getHydrator('stdClass');
        }
        return self::$hydrators[$class] = static function ($properties, $objects) use($propertySetters) {
            foreach ($properties as $name => $values) {
                if ($setValue = isset($propertySetters[$name]) ? $propertySetters[$name] : null) {
                    foreach ($values as $i => $v) {
                        $setValue($objects[$i], $v);
                    }
                    continue;
                }
                foreach ($values as $i => $v) {
                    $objects[$i]->{$name} = $v;
                }
            }
        };
    }
}
class Anonymous__b3dd2147f1529ab4e7eddc492e2855ce__0 extends \ErrorException
{
}
class Anonymous__b3dd2147f1529ab4e7eddc492e2855ce__1 extends \Error
{
}
