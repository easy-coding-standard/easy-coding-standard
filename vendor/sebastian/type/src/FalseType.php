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

final class FalseType extends \ECSPrefix20210804\SebastianBergmann\Type\Type
{
    /**
     * @param \SebastianBergmann\Type\Type $other
     */
    public function isAssignable($other) : bool
    {
        if ($other instanceof self) {
            return \true;
        }
        return $other instanceof \ECSPrefix20210804\SebastianBergmann\Type\SimpleType && $other->name() === 'bool' && $other->value() === \false;
    }
    public function name() : string
    {
        return 'false';
    }
    public function allowsNull() : bool
    {
        return \false;
    }
    /**
     * @deprecated
     *
     * @codeCoverageIgnore
     *
     * @throws LogicException
     */
    public function getReturnTypeDeclaration() : string
    {
        throw new \ECSPrefix20210804\SebastianBergmann\Type\LogicException();
    }
}
