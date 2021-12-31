<?php

declare (strict_types=1);
namespace ECSPrefix20211231\Symplify\PackageBuilder\Reflection;

use ReflectionProperty;
use ECSPrefix20211231\Symplify\PHPStanRules\Exception\ShouldNotHappenException;
/**
 * @api
 * @see \Symplify\PackageBuilder\Tests\Reflection\PrivatesAccessorTest
 */
final class PrivatesAccessor
{
    /**
     * @template T
     *
     * @param class-string<T> $valueClassName
     *
     * @return object&mixed
     * @param object $object
     */
    public function getPrivatePropertyOfClass($object, string $propertyName, string $valueClassName)
    {
        $value = $this->getPrivateProperty($object, $propertyName);
        if ($value instanceof $valueClassName) {
            return $value;
        }
        throw new \ECSPrefix20211231\Symplify\PHPStanRules\Exception\ShouldNotHappenException();
    }
    /**
     * @return mixed
     * @param object $object
     */
    public function getPrivateProperty($object, string $propertyName)
    {
        $propertyReflection = $this->resolvePropertyReflection($object, $propertyName);
        $propertyReflection->setAccessible(\true);
        return $propertyReflection->getValue($object);
    }
    /**
     * @template T
     *
     * @param class-string<T> $valueClassName
     * @param mixed $value
     * @param object $object
     */
    public function setPrivatePropertyOfClass($object, string $propertyName, $value, string $valueClassName) : void
    {
        if (!$value instanceof $valueClassName) {
            throw new \ECSPrefix20211231\Symplify\PHPStanRules\Exception\ShouldNotHappenException();
        }
        $this->setPrivateProperty($object, $propertyName, $value);
    }
    /**
     * @param mixed $value
     * @param object $object
     */
    public function setPrivateProperty($object, string $propertyName, $value) : void
    {
        $propertyReflection = $this->resolvePropertyReflection($object, $propertyName);
        $propertyReflection->setAccessible(\true);
        $propertyReflection->setValue($object, $value);
    }
    /**
     * @param object $object
     */
    private function resolvePropertyReflection($object, string $propertyName) : \ReflectionProperty
    {
        if (\property_exists($object, $propertyName)) {
            return new \ReflectionProperty($object, $propertyName);
        }
        $parentClass = \get_parent_class($object);
        if ($parentClass === \false) {
            $errorMessage = \sprintf('Property "$%s" was not found in "%s" class', $propertyName, \get_class($object));
            throw new \ECSPrefix20211231\Symplify\PHPStanRules\Exception\ShouldNotHappenException($errorMessage);
        }
        return new \ReflectionProperty($parentClass, $propertyName);
    }
}
