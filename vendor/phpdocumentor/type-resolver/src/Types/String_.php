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
 * Value Object representing the type 'string'.
 *
 * @psalm-immutable
 */
final class String_ implements \ECSPrefix20210804\phpDocumentor\Reflection\Type
{
    /**
     * Returns a rendered output of the Type as it would be used in a DocBlock.
     */
    public function __toString() : string
    {
        return 'string';
    }
}
