<?php

namespace ECSPrefix20210515\Symplify\PackageBuilder\Reflection;

use ReflectionProperty;
use ECSPrefix20210515\Symplify\PHPStanRules\Exception\ShouldNotHappenException;
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
        $propertyName = (string) $propertyName;
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
        $propertyName = (string) $propertyName;
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
        $propertyName = (string) $propertyName;
        if (\property_exists($object, $propertyName)) {
            return new \ReflectionProperty($object, $propertyName);
        }
        $parentClass = \get_parent_class($object);
        if ($parentClass === \false) {
            throw new \ECSPrefix20210515\Symplify\PHPStanRules\Exception\ShouldNotHappenException();
        }
        return new \ReflectionProperty($parentClass, $propertyName);
    }
}
