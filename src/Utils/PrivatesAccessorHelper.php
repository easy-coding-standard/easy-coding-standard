<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Utils;

use ReflectionProperty;
final class PrivatesAccessorHelper
{
    /**
     * @return mixed
     */
    public static function getPropertyValue(object $object, string $propertyName)
    {
        $reflectionProperty = new ReflectionProperty($object, $propertyName);
        // this is needed for PHP 7.2 downgrade code
        $reflectionProperty->setAccessible(\true);
        return $reflectionProperty->getValue($object);
    }
    /**
     * @param mixed $value
     */
    public static function setPropertyValue(object $object, string $propertyName, $value) : void
    {
        $reflectionProperty = new ReflectionProperty($object, $propertyName);
        // this is needed for PHP 7.2 downgrade code
        $reflectionProperty->setAccessible(\true);
        $reflectionProperty->setValue($object, $value);
    }
}
