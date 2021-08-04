<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/code-unit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\CodeUnit;

use function range;
use function sprintf;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
/**
 * @psalm-immutable
 */
abstract class CodeUnit
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $sourceFileName;
    /**
     * @var array
     * @psalm-var list<int>
     */
    private $sourceLines;
    /**
     * @psalm-param class-string $className
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     * @param string $className
     */
    public static function forClass($className) : \ECSPrefix20210804\SebastianBergmann\CodeUnit\ClassUnit
    {
        self::ensureUserDefinedClass($className);
        $reflector = self::reflectorForClass($className);
        return new \ECSPrefix20210804\SebastianBergmann\CodeUnit\ClassUnit($className, $reflector->getFileName(), \range($reflector->getStartLine(), $reflector->getEndLine()));
    }
    /**
     * @psalm-param class-string $className
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     * @param string $className
     * @param string $methodName
     */
    public static function forClassMethod($className, $methodName) : \ECSPrefix20210804\SebastianBergmann\CodeUnit\ClassMethodUnit
    {
        self::ensureUserDefinedClass($className);
        $reflector = self::reflectorForClassMethod($className, $methodName);
        return new \ECSPrefix20210804\SebastianBergmann\CodeUnit\ClassMethodUnit($className . '::' . $methodName, $reflector->getFileName(), \range($reflector->getStartLine(), $reflector->getEndLine()));
    }
    /**
     * @psalm-param class-string $interfaceName
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     * @param string $interfaceName
     */
    public static function forInterface($interfaceName) : \ECSPrefix20210804\SebastianBergmann\CodeUnit\InterfaceUnit
    {
        self::ensureUserDefinedInterface($interfaceName);
        $reflector = self::reflectorForClass($interfaceName);
        return new \ECSPrefix20210804\SebastianBergmann\CodeUnit\InterfaceUnit($interfaceName, $reflector->getFileName(), \range($reflector->getStartLine(), $reflector->getEndLine()));
    }
    /**
     * @psalm-param class-string $interfaceName
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     * @param string $interfaceName
     * @param string $methodName
     */
    public static function forInterfaceMethod($interfaceName, $methodName) : \ECSPrefix20210804\SebastianBergmann\CodeUnit\InterfaceMethodUnit
    {
        self::ensureUserDefinedInterface($interfaceName);
        $reflector = self::reflectorForClassMethod($interfaceName, $methodName);
        return new \ECSPrefix20210804\SebastianBergmann\CodeUnit\InterfaceMethodUnit($interfaceName . '::' . $methodName, $reflector->getFileName(), \range($reflector->getStartLine(), $reflector->getEndLine()));
    }
    /**
     * @psalm-param class-string $traitName
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     * @param string $traitName
     */
    public static function forTrait($traitName) : \ECSPrefix20210804\SebastianBergmann\CodeUnit\TraitUnit
    {
        self::ensureUserDefinedTrait($traitName);
        $reflector = self::reflectorForClass($traitName);
        return new \ECSPrefix20210804\SebastianBergmann\CodeUnit\TraitUnit($traitName, $reflector->getFileName(), \range($reflector->getStartLine(), $reflector->getEndLine()));
    }
    /**
     * @psalm-param class-string $traitName
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     * @param string $traitName
     * @param string $methodName
     */
    public static function forTraitMethod($traitName, $methodName) : \ECSPrefix20210804\SebastianBergmann\CodeUnit\TraitMethodUnit
    {
        self::ensureUserDefinedTrait($traitName);
        $reflector = self::reflectorForClassMethod($traitName, $methodName);
        return new \ECSPrefix20210804\SebastianBergmann\CodeUnit\TraitMethodUnit($traitName . '::' . $methodName, $reflector->getFileName(), \range($reflector->getStartLine(), $reflector->getEndLine()));
    }
    /**
     * @psalm-param callable-string $functionName
     *
     * @throws InvalidCodeUnitException
     * @throws ReflectionException
     * @param string $functionName
     */
    public static function forFunction($functionName) : \ECSPrefix20210804\SebastianBergmann\CodeUnit\FunctionUnit
    {
        $reflector = self::reflectorForFunction($functionName);
        if (!$reflector->isUserDefined()) {
            throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\InvalidCodeUnitException(\sprintf('"%s" is not a user-defined function', $functionName));
        }
        return new \ECSPrefix20210804\SebastianBergmann\CodeUnit\FunctionUnit($functionName, $reflector->getFileName(), \range($reflector->getStartLine(), $reflector->getEndLine()));
    }
    /**
     * @psalm-param list<int> $sourceLines
     */
    private function __construct(string $name, string $sourceFileName, array $sourceLines)
    {
        $this->name = $name;
        $this->sourceFileName = $sourceFileName;
        $this->sourceLines = $sourceLines;
    }
    public function name() : string
    {
        return $this->name;
    }
    public function sourceFileName() : string
    {
        return $this->sourceFileName;
    }
    /**
     * @psalm-return list<int>
     */
    public function sourceLines() : array
    {
        return $this->sourceLines;
    }
    public function isClass() : bool
    {
        return \false;
    }
    public function isClassMethod() : bool
    {
        return \false;
    }
    public function isInterface() : bool
    {
        return \false;
    }
    public function isInterfaceMethod() : bool
    {
        return \false;
    }
    public function isTrait() : bool
    {
        return \false;
    }
    public function isTraitMethod() : bool
    {
        return \false;
    }
    public function isFunction() : bool
    {
        return \false;
    }
    /**
     * @psalm-param class-string $className
     *
     * @throws InvalidCodeUnitException
     * @return void
     */
    private static function ensureUserDefinedClass(string $className)
    {
        try {
            $reflector = new \ReflectionClass($className);
            if ($reflector->isInterface()) {
                throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\InvalidCodeUnitException(\sprintf('"%s" is an interface and not a class', $className));
            }
            if ($reflector->isTrait()) {
                throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\InvalidCodeUnitException(\sprintf('"%s" is a trait and not a class', $className));
            }
            if (!$reflector->isUserDefined()) {
                throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\InvalidCodeUnitException(\sprintf('"%s" is not a user-defined class', $className));
            }
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\ReflectionException($e->getMessage(), (int) $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    /**
     * @psalm-param class-string $interfaceName
     *
     * @throws InvalidCodeUnitException
     * @return void
     */
    private static function ensureUserDefinedInterface(string $interfaceName)
    {
        try {
            $reflector = new \ReflectionClass($interfaceName);
            if (!$reflector->isInterface()) {
                throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\InvalidCodeUnitException(\sprintf('"%s" is not an interface', $interfaceName));
            }
            if (!$reflector->isUserDefined()) {
                throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\InvalidCodeUnitException(\sprintf('"%s" is not a user-defined interface', $interfaceName));
            }
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\ReflectionException($e->getMessage(), (int) $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    /**
     * @psalm-param class-string $traitName
     *
     * @throws InvalidCodeUnitException
     * @return void
     */
    private static function ensureUserDefinedTrait(string $traitName)
    {
        try {
            $reflector = new \ReflectionClass($traitName);
            if (!$reflector->isTrait()) {
                throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\InvalidCodeUnitException(\sprintf('"%s" is not a trait', $traitName));
            }
            // @codeCoverageIgnoreStart
            if (!$reflector->isUserDefined()) {
                throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\InvalidCodeUnitException(\sprintf('"%s" is not a user-defined trait', $traitName));
            }
        } catch (\ReflectionException $e) {
            throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\ReflectionException($e->getMessage(), (int) $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    /**
     * @psalm-param class-string $className
     *
     * @throws ReflectionException
     */
    private static function reflectorForClass(string $className) : \ReflectionClass
    {
        try {
            return new \ReflectionClass($className);
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\ReflectionException($e->getMessage(), (int) $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    /**
     * @psalm-param class-string $className
     *
     * @throws ReflectionException
     */
    private static function reflectorForClassMethod(string $className, string $methodName) : \ReflectionMethod
    {
        try {
            return new \ReflectionMethod($className, $methodName);
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\ReflectionException($e->getMessage(), (int) $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    /**
     * @psalm-param callable-string $functionName
     *
     * @throws ReflectionException
     */
    private static function reflectorForFunction(string $functionName) : \ReflectionFunction
    {
        try {
            return new \ReflectionFunction($functionName);
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new \ECSPrefix20210804\SebastianBergmann\CodeUnit\ReflectionException($e->getMessage(), (int) $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
}
