<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/type.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\SebastianBergmann\Type;

use function get_class;
use function gettype;
use function strtolower;
abstract class Type
{
    /**
     * @return $this
     * @param bool $allowsNull
     */
    public static function fromValue($value, $allowsNull)
    {
        if ($value === \false) {
            return new \ECSPrefix20210803\SebastianBergmann\Type\FalseType();
        }
        $typeName = \gettype($value);
        if ($typeName === 'object') {
            return new \ECSPrefix20210803\SebastianBergmann\Type\ObjectType(\ECSPrefix20210803\SebastianBergmann\Type\TypeName::fromQualifiedName(\get_class($value)), $allowsNull);
        }
        $type = self::fromName($typeName, $allowsNull);
        if ($type instanceof \ECSPrefix20210803\SebastianBergmann\Type\SimpleType) {
            $type = new \ECSPrefix20210803\SebastianBergmann\Type\SimpleType($typeName, $allowsNull, $value);
        }
        return $type;
    }
    /**
     * @return $this
     * @param string $typeName
     * @param bool $allowsNull
     */
    public static function fromName($typeName, $allowsNull)
    {
        switch (\strtolower($typeName)) {
            case 'callable':
                return new \ECSPrefix20210803\SebastianBergmann\Type\CallableType($allowsNull);
            case 'false':
                return new \ECSPrefix20210803\SebastianBergmann\Type\FalseType();
            case 'iterable':
                return new \ECSPrefix20210803\SebastianBergmann\Type\IterableType($allowsNull);
            case 'null':
                return new \ECSPrefix20210803\SebastianBergmann\Type\NullType();
            case 'object':
                return new \ECSPrefix20210803\SebastianBergmann\Type\GenericObjectType($allowsNull);
            case 'unknown type':
                return new \ECSPrefix20210803\SebastianBergmann\Type\UnknownType();
            case 'void':
                return new \ECSPrefix20210803\SebastianBergmann\Type\VoidType();
            case 'array':
            case 'bool':
            case 'boolean':
            case 'double':
            case 'float':
            case 'int':
            case 'integer':
            case 'real':
            case 'resource':
            case 'resource (closed)':
            case 'string':
                return new \ECSPrefix20210803\SebastianBergmann\Type\SimpleType($typeName, $allowsNull);
            default:
                return new \ECSPrefix20210803\SebastianBergmann\Type\ObjectType(\ECSPrefix20210803\SebastianBergmann\Type\TypeName::fromQualifiedName($typeName), $allowsNull);
        }
    }
    public function asString() : string
    {
        return ($this->allowsNull() ? '?' : '') . $this->name();
    }
    /**
     * @deprecated
     *
     * @codeCoverageIgnore
     */
    public function getReturnTypeDeclaration() : string
    {
        return ': ' . $this->asString();
    }
    /**
     * @param \SebastianBergmann\Type\Type $other
     */
    public abstract function isAssignable($other) : bool;
    public abstract function name() : string;
    public abstract function allowsNull() : bool;
}
