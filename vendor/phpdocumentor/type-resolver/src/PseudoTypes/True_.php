<?php

declare (strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */
namespace ECSPrefix20210804\phpDocumentor\Reflection\PseudoTypes;

use ECSPrefix20210804\phpDocumentor\Reflection\PseudoType;
use ECSPrefix20210804\phpDocumentor\Reflection\Type;
use ECSPrefix20210804\phpDocumentor\Reflection\Types\Boolean;
use function class_alias;
/**
 * Value Object representing the PseudoType 'False', which is a Boolean type.
 *
 * @psalm-immutable
 */
final class True_ extends \ECSPrefix20210804\phpDocumentor\Reflection\Types\Boolean implements \ECSPrefix20210804\phpDocumentor\Reflection\PseudoType
{
    public function underlyingType() : \ECSPrefix20210804\phpDocumentor\Reflection\Type
    {
        return new \ECSPrefix20210804\phpDocumentor\Reflection\Types\Boolean();
    }
    public function __toString() : string
    {
        return 'true';
    }
}
\class_alias('ECSPrefix20210804\\phpDocumentor\\Reflection\\PseudoTypes\\True_', 'ECSPrefix20210804\\phpDocumentor\\Reflection\\Types\\True_', \false);
