<?php

declare (strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */
namespace ECSPrefix20210804\phpDocumentor\Reflection\Types;

use ECSPrefix20210804\phpDocumentor\Reflection\Type;
/**
 * Value Object representing the 'parent' type.
 *
 * Parent, as a Type, represents the parent class of class in which the associated element was defined.
 *
 * @psalm-immutable
 */
final class Parent_ implements \ECSPrefix20210804\phpDocumentor\Reflection\Type
{
    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString() : string
    {
        return 'parent';
    }
}
