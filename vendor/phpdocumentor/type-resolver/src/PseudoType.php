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
namespace ECSPrefix20210803\phpDocumentor\Reflection;

interface PseudoType extends \ECSPrefix20210803\phpDocumentor\Reflection\Type
{
    public function underlyingType() : \ECSPrefix20210803\phpDocumentor\Reflection\Type;
}
