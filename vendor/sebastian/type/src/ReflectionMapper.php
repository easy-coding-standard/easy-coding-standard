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

use function assert;
use function sprintf;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;
final class ReflectionMapper
{
    public function fromMethodReturnType(\ReflectionMethod $method) : \ECSPrefix20210803\SebastianBergmann\Type\Type
    {
        if (!$this->reflectionMethodHasReturnType($method)) {
            return new \ECSPrefix20210803\SebastianBergmann\Type\UnknownType();
        }
        $returnType = $this->reflectionMethodGetReturnType($method);
        \assert($returnType instanceof \ReflectionNamedType || $returnType instanceof \ReflectionUnionType);
        if ($returnType instanceof \ReflectionNamedType) {
            if ($returnType->getName() === 'self') {
                return \ECSPrefix20210803\SebastianBergmann\Type\ObjectType::fromName($method->getDeclaringClass()->getName(), $returnType->allowsNull());
            }
            if ($returnType->getName() === 'static') {
                return new \ECSPrefix20210803\SebastianBergmann\Type\StaticType(\ECSPrefix20210803\SebastianBergmann\Type\TypeName::fromReflection($method->getDeclaringClass()), $returnType->allowsNull());
            }
            if ($returnType->getName() === 'mixed') {
                return new \ECSPrefix20210803\SebastianBergmann\Type\MixedType();
            }
            if ($returnType->getName() === 'parent') {
                $parentClass = $method->getDeclaringClass()->getParentClass();
                // @codeCoverageIgnoreStart
                if ($parentClass === \false) {
                    throw new \ECSPrefix20210803\SebastianBergmann\Type\RuntimeException(\sprintf('%s::%s() has a "parent" return type declaration but %s does not have a parent class', $method->getDeclaringClass()->getName(), $method->getName(), $method->getDeclaringClass()->getName()));
                }
                // @codeCoverageIgnoreEnd
                return \ECSPrefix20210803\SebastianBergmann\Type\ObjectType::fromName($parentClass->getName(), $returnType->allowsNull());
            }
            return \ECSPrefix20210803\SebastianBergmann\Type\Type::fromName($returnType->getName(), $returnType->allowsNull());
        }
        \assert($returnType instanceof \ReflectionUnionType);
        $types = [];
        foreach ($returnType->getTypes() as $type) {
            \assert($type instanceof \ReflectionNamedType);
            if ($type->getName() === 'self') {
                $types[] = \ECSPrefix20210803\SebastianBergmann\Type\ObjectType::fromName($method->getDeclaringClass()->getName(), \false);
            } else {
                $types[] = \ECSPrefix20210803\SebastianBergmann\Type\Type::fromName($type->getName(), \false);
            }
        }
        return new \ECSPrefix20210803\SebastianBergmann\Type\UnionType(...$types);
    }
    private function reflectionMethodHasReturnType(\ReflectionMethod $method) : bool
    {
        if ($method->hasReturnType()) {
            return \true;
        }
        if (!\method_exists($method, 'hasTentativeReturnType')) {
            return \false;
        }
        return $method->hasTentativeReturnType();
    }
    /**
     * @return \ReflectionType|null
     */
    private function reflectionMethodGetReturnType(\ReflectionMethod $method)
    {
        if ($method->hasReturnType()) {
            return $method->getReturnType();
        }
        if (!\method_exists($method, 'getTentativeReturnType')) {
            return null;
        }
        return $method->getTentativeReturnType();
    }
}
