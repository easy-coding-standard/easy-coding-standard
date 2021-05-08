<?php

namespace Symplify\PackageBuilder\Reflection;

use ReflectionProperty;
use Symplify\PHPStanRules\Exception\ShouldNotHappenException;
/**
 * @see \Symplify\PackageBuilder\Tests\Reflection\PrivatesAccessorTest
 */
final class PrivatesAccessor
{
    /**
     * @return mixed
     * @param object $object
     * @param string $propertyName
     */
    public function getPrivateProperty($object, $propertyName)
    {
        if (\is_object($propertyName)) {
            $propertyName = (string) $propertyName;
        }
        $propertyReflection = $this->resolvePropertyReflection($object, $propertyName);
        $propertyReflection->setAccessible(\true);
        return $propertyReflection->getValue($object);
    }
    /**
     * @param object $object
     * @return void
     * @param string $propertyName
     */
    public function setPrivateProperty($object, $propertyName, $value)
    {
        if (\is_object($propertyName)) {
            $propertyName = (string) $propertyName;
        }
        $propertyReflection = $this->resolvePropertyReflection($object, $propertyName);
        $propertyReflection->setAccessible(\true);
        $propertyReflection->setValue($object, $value);
    }
    /**
     * @param object $object
     * @param string $propertyName
     * @return \ReflectionProperty
     */
    private function resolvePropertyReflection($object, $propertyName)
    {
        if (\is_object($propertyName)) {
            $propertyName = (string) $propertyName;
        }
        if (\property_exists($object, $propertyName)) {
            return new \ReflectionProperty($object, $propertyName);
        }
        $parentClass = \get_parent_class($object);
        if ($parentClass === \false) {
            throw new \Symplify\PHPStanRules\Exception\ShouldNotHappenException();
        }
        return new \ReflectionProperty($parentClass, $propertyName);
    }
}
