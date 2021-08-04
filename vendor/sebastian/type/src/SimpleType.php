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
namespace ECSPrefix20210804\SebastianBergmann\Type;

use function strtolower;
final class SimpleType extends \ECSPrefix20210804\SebastianBergmann\Type\Type
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var bool
     */
    private $allowsNull;
    /**
     * @var mixed
     */
    private $value;
    public function __construct(string $name, bool $nullable, $value = null)
    {
        $this->name = $this->normalize($name);
        $this->allowsNull = $nullable;
        $this->value = $value;
    }
    /**
     * @param \SebastianBergmann\Type\Type $other
     */
    public function isAssignable($other) : bool
    {
        if ($this->allowsNull && $other instanceof \ECSPrefix20210804\SebastianBergmann\Type\NullType) {
            return \true;
        }
        if ($this->name === 'bool' && $other->name() === 'false') {
            return \true;
        }
        if ($other instanceof self) {
            return $this->name === $other->name;
        }
        return \false;
    }
    public function name() : string
    {
        return $this->name;
    }
    public function allowsNull() : bool
    {
        return $this->allowsNull;
    }
    public function value()
    {
        return $this->value;
    }
    private function normalize(string $name) : string
    {
        $name = \strtolower($name);
        switch ($name) {
            case 'boolean':
                return 'bool';
            case 'real':
            case 'double':
                return 'float';
            case 'integer':
                return 'int';
            case '[]':
                return 'array';
            default:
                return $name;
        }
    }
}
