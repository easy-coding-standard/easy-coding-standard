<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\PHPUnit\Framework\Constraint;

use function get_class;
use function is_object;
use ECSPrefix20210803\PHPUnit\Framework\ActualValueIsNotAnObjectException;
use ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotAcceptParameterTypeException;
use ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotDeclareBoolReturnTypeException;
use ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotDeclareExactlyOneParameterException;
use ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotDeclareParameterTypeException;
use ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotExistException;
use ReflectionNamedType;
use ReflectionObject;
/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class ObjectEquals extends \ECSPrefix20210803\PHPUnit\Framework\Constraint\Constraint
{
    /**
     * @var object
     */
    private $expected;
    /**
     * @var string
     */
    private $method;
    public function __construct(object $object, string $method = 'equals')
    {
        $this->expected = $object;
        $this->method = $method;
    }
    public function toString() : string
    {
        return 'two objects are equal';
    }
    /**
     * @throws ActualValueIsNotAnObjectException
     * @throws ComparisonMethodDoesNotAcceptParameterTypeException
     * @throws ComparisonMethodDoesNotDeclareBoolReturnTypeException
     * @throws ComparisonMethodDoesNotDeclareExactlyOneParameterException
     * @throws ComparisonMethodDoesNotDeclareParameterTypeException
     * @throws ComparisonMethodDoesNotExistException
     */
    protected function matches($other) : bool
    {
        if (!\is_object($other)) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ActualValueIsNotAnObjectException();
        }
        $object = new \ReflectionObject($other);
        if (!$object->hasMethod($this->method)) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotExistException(\get_class($other), $this->method);
        }
        /** @noinspection PhpUnhandledExceptionInspection */
        $method = $object->getMethod($this->method);
        if (!$method->hasReturnType()) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotDeclareBoolReturnTypeException(\get_class($other), $this->method);
        }
        $returnType = $method->getReturnType();
        if (!$returnType instanceof \ReflectionNamedType) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotDeclareBoolReturnTypeException(\get_class($other), $this->method);
        }
        if ($returnType->allowsNull()) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotDeclareBoolReturnTypeException(\get_class($other), $this->method);
        }
        if ($returnType->getName() !== 'bool') {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotDeclareBoolReturnTypeException(\get_class($other), $this->method);
        }
        if ($method->getNumberOfParameters() !== 1 || $method->getNumberOfRequiredParameters() !== 1) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotDeclareExactlyOneParameterException(\get_class($other), $this->method);
        }
        $parameter = $method->getParameters()[0];
        if (!$parameter->hasType()) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotDeclareParameterTypeException(\get_class($other), $this->method);
        }
        $type = $parameter->getType();
        if (!$type instanceof \ReflectionNamedType) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotDeclareParameterTypeException(\get_class($other), $this->method);
        }
        $typeName = $type->getName();
        if ($typeName === 'self') {
            $typeName = \get_class($other);
        }
        if (!$this->expected instanceof $typeName) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\ComparisonMethodDoesNotAcceptParameterTypeException(\get_class($other), $this->method, \get_class($this->expected));
        }
        return $other->{$this->method}($this->expected);
    }
    protected function failureDescription($other) : string
    {
        return $this->toString();
    }
}
