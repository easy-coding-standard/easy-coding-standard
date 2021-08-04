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

final class UnknownType extends \ECSPrefix20210804\SebastianBergmann\Type\Type
{
    /**
     * @param \SebastianBergmann\Type\Type $other
     */
    public function isAssignable($other) : bool
    {
        return \true;
    }
    public function name() : string
    {
        return 'unknown type';
    }
    public function asString() : string
    {
        return '';
    }
    /**
     * @deprecated
     *
     * @codeCoverageIgnore
     */
    public function getReturnTypeDeclaration() : string
    {
        return '';
    }
    public function allowsNull() : bool
    {
        return \true;
    }
}
