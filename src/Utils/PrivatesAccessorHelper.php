<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Utils;

use ReflectionProperty;

final class PrivatesAccessorHelper
{
    public static function getPropertyValue(object $object, string $property): mixed
    {
        $reflectionProperty = new ReflectionProperty($object, $property);

        return $reflectionProperty->getValue($object);
    }
}
