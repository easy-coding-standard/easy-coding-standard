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

use function is_subclass_of;
use function strcasecmp;
final class ObjectType extends \ECSPrefix20210803\SebastianBergmann\Type\Type
{
    /**
     * @var TypeName
     */
    private $className;
    /**
     * @var bool
     */
    private $allowsNull;
    public function __construct(\ECSPrefix20210803\SebastianBergmann\Type\TypeName $className, bool $allowsNull)
    {
        $this->className = $className;
        $this->allowsNull = $allowsNull;
    }
    /**
     * @param \SebastianBergmann\Type\Type $other
     */
    public function isAssignable($other) : bool
    {
        if ($this->allowsNull && $other instanceof \ECSPrefix20210803\SebastianBergmann\Type\NullType) {
            return \true;
        }
        if ($other instanceof self) {
            if (0 === \strcasecmp($this->className->qualifiedName(), $other->className->qualifiedName())) {
                return \true;
            }
            if (\is_subclass_of($other->className->qualifiedName(), $this->className->qualifiedName(), \true)) {
                return \true;
            }
        }
        return \false;
    }
    public function name() : string
    {
        return $this->className->qualifiedName();
    }
    public function allowsNull() : bool
    {
        return $this->allowsNull;
    }
    public function className() : \ECSPrefix20210803\SebastianBergmann\Type\TypeName
    {
        return $this->className;
    }
}
