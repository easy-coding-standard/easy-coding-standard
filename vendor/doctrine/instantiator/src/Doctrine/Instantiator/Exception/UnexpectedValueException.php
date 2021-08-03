<?php

namespace ECSPrefix20210803\Doctrine\Instantiator\Exception;

use Exception;
use ReflectionClass;
use UnexpectedValueException as BaseUnexpectedValueException;
use function sprintf;
/**
 * Exception for given parameters causing invalid/unexpected state on instantiation
 */
class UnexpectedValueException extends \UnexpectedValueException implements \ECSPrefix20210803\Doctrine\Instantiator\Exception\ExceptionInterface
{
    /**
     * @template T of object
     * @phpstan-param ReflectionClass<T> $reflectionClass
     * @return $this
     * @param \ReflectionClass $reflectionClass
     * @param \Exception $exception
     */
    public static function fromSerializationTriggeredException($reflectionClass, $exception)
    {
        return new self(\sprintf('An exception was raised while trying to instantiate an instance of "%s" via un-serialization', $reflectionClass->getName()), 0, $exception);
    }
    /**
     * @template T of object
     * @phpstan-param ReflectionClass<T> $reflectionClass
     * @return $this
     * @param \ReflectionClass $reflectionClass
     * @param string $errorString
     * @param int $errorCode
     * @param string $errorFile
     * @param int $errorLine
     */
    public static function fromUncleanUnSerialization($reflectionClass, $errorString, $errorCode, $errorFile, $errorLine)
    {
        return new self(\sprintf('Could not produce an instance of "%s" via un-serialization, since an error was triggered ' . 'in file "%s" at line "%d"', $reflectionClass->getName(), $errorFile, $errorLine), 0, new \Exception($errorString, $errorCode));
    }
}
