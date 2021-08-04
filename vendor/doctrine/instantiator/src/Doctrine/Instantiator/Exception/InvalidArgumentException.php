<?php

namespace ECSPrefix20210804\Doctrine\Instantiator\Exception;

use InvalidArgumentException as BaseInvalidArgumentException;
use ReflectionClass;
use function interface_exists;
use function sprintf;
use function trait_exists;
/**
 * Exception for invalid arguments provided to the instantiator
 */
class InvalidArgumentException extends \InvalidArgumentException implements \ECSPrefix20210804\Doctrine\Instantiator\Exception\ExceptionInterface
{
    /**
     * @return $this
     * @param string $className
     */
    public static function fromNonExistingClass($className)
    {
        if (\interface_exists($className)) {
            return new self(\sprintf('The provided type "%s" is an interface, and can not be instantiated', $className));
        }
        if (\trait_exists($className)) {
            return new self(\sprintf('The provided type "%s" is a trait, and can not be instantiated', $className));
        }
        return new self(\sprintf('The provided class "%s" does not exist', $className));
    }
    /**
     * @template T of object
     * @phpstan-param ReflectionClass<T> $reflectionClass
     * @return $this
     * @param \ReflectionClass $reflectionClass
     */
    public static function fromAbstractClass($reflectionClass)
    {
        return new self(\sprintf('The provided class "%s" is abstract, and can not be instantiated', $reflectionClass->getName()));
    }
}
