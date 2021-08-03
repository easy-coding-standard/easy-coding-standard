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

final class GenericObjectType extends \ECSPrefix20210803\SebastianBergmann\Type\Type
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
     * @param \SebastianBergmann\Type\Type $other
     */
    public function isAssignable($other) : bool
    {
        if ($this->allowsNull && $other instanceof \ECSPrefix20210803\SebastianBergmann\Type\NullType) {
            return \true;
        }
        if (!$other instanceof \ECSPrefix20210803\SebastianBergmann\Type\ObjectType) {
            return \false;
        }
        return \true;
    }
    public function name() : string
    {
        return 'object';
    }
    public function allowsNull() : bool
    {
        return $this->allowsNull;
    }
}
