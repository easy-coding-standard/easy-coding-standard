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

final class MixedType extends \ECSPrefix20210804\SebastianBergmann\Type\Type
{
    /**
     * @param \SebastianBergmann\Type\Type $other
     */
    public function isAssignable($other) : bool
    {
        return !$other instanceof \ECSPrefix20210804\SebastianBergmann\Type\VoidType;
    }
    public function asString() : string
    {
        return 'mixed';
    }
    public function name() : string
    {
        return 'mixed';
    }
    public function allowsNull() : bool
    {
        return \true;
    }
}
