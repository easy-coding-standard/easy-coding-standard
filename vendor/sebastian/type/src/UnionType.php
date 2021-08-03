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

use function count;
use function implode;
use function sort;
final class UnionType extends \ECSPrefix20210803\SebastianBergmann\Type\Type
{
    /**
     * @psalm-var list<Type>
     */
    private $types;
    /**
     * @throws RuntimeException
     */
    public function __construct(\ECSPrefix20210803\SebastianBergmann\Type\Type ...$types)
    {
        $this->ensureMinimumOfTwoTypes(...$types);
        $this->ensureOnlyValidTypes(...$types);
        $this->types = $types;
    }
    /**
     * @param \SebastianBergmann\Type\Type $other
     */
    public function isAssignable($other) : bool
    {
        foreach ($this->types as $type) {
            if ($type->isAssignable($other)) {
                return \true;
            }
        }
        return \false;
    }
    public function asString() : string
    {
        return $this->name();
    }
    /**
     * @deprecated
     *
     * @codeCoverageIgnore
     */
    public function getReturnTypeDeclaration() : string
    {
        return ': ' . $this->name();
    }
    public function name() : string
    {
        $types = [];
        foreach ($this->types as $type) {
            $types[] = $type->name();
        }
        \sort($types);
        return \implode('|', $types);
    }
    public function allowsNull() : bool
    {
        foreach ($this->types as $type) {
            if ($type instanceof \ECSPrefix20210803\SebastianBergmann\Type\NullType) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @throws RuntimeException
     * @return void
     */
    private function ensureMinimumOfTwoTypes(\ECSPrefix20210803\SebastianBergmann\Type\Type ...$types)
    {
        if (\count($types) < 2) {
            throw new \ECSPrefix20210803\SebastianBergmann\Type\RuntimeException('A union type must be composed of at least two types');
        }
    }
    /**
     * @throws RuntimeException
     * @return void
     */
    private function ensureOnlyValidTypes(\ECSPrefix20210803\SebastianBergmann\Type\Type ...$types)
    {
        foreach ($types as $type) {
            if ($type instanceof \ECSPrefix20210803\SebastianBergmann\Type\UnknownType) {
                throw new \ECSPrefix20210803\SebastianBergmann\Type\RuntimeException('A union type must not be composed of an unknown type');
            }
            if ($type instanceof \ECSPrefix20210803\SebastianBergmann\Type\VoidType) {
                throw new \ECSPrefix20210803\SebastianBergmann\Type\RuntimeException('A union type must not be composed of a void type');
            }
        }
    }
}
