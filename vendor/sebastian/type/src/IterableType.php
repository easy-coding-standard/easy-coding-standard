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
use function class_exists;
use function is_iterable;
use ReflectionClass;
use ReflectionException;
final class IterableType extends \ECSPrefix20210803\SebastianBergmann\Type\Type
{
    /**
     * @var bool
     */
    private $allowsNull;
    public function __construct(bool $nullable)
    {
        $this->allowsNull = $nullable;
    }
    /**
     * @throws RuntimeException
     * @param \SebastianBergmann\Type\Type $other
     */
    public function isAssignable($other) : bool
    {
        if ($this->allowsNull && $other instanceof \ECSPrefix20210803\SebastianBergmann\Type\NullType) {
            return \true;
        }
        if ($other instanceof self) {
            return \true;
        }
        if ($other instanceof \ECSPrefix20210803\SebastianBergmann\Type\SimpleType) {
            return \is_array($other->value()) || $other->value() instanceof \Traversable;
        }
        if ($other instanceof \ECSPrefix20210803\SebastianBergmann\Type\ObjectType) {
            $className = $other->className()->qualifiedName();
            \assert(\class_exists($className));
            try {
                return (new \ReflectionClass($className))->isIterable();
                // @codeCoverageIgnoreStart
            } catch (\ReflectionException $e) {
                throw new \ECSPrefix20210803\SebastianBergmann\Type\RuntimeException($e->getMessage(), (int) $e->getCode(), $e);
                // @codeCoverageIgnoreEnd
            }
        }
        return \false;
    }
    public function name() : string
    {
        return 'iterable';
    }
    public function allowsNull() : bool
    {
        return $this->allowsNull;
    }
}
