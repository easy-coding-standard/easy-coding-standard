<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Utils;

use ReflectionProperty;

final class PrivatesAccessorHelper
{
    public static function getPropertyValue(object $object, string $propertyName): mixed
    {
        $reflectionProperty = new ReflectionProperty($object, $propertyName);

        return $reflectionProperty->getValue($object);
    }

    public static function setPropertyValue(object $object, string $propertyName, mixed $value): void
    {
        $reflectionProperty = new ReflectionProperty($object, $propertyName);
        $reflectionProperty->setValue($object, $value);
    }
}
