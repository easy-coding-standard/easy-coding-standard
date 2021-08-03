<?php

declare (strict_types=1);
/*
 * This file is part of sebastian/code-unit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210803\SebastianBergmann\CodeUnit;

/**
 * @psalm-immutable
 */
final class ClassMethodUnit extends \ECSPrefix20210803\SebastianBergmann\CodeUnit\CodeUnit
{
    /**
     * @psalm-assert-if-true ClassMethodUnit $this
     */
    public function isClassMethod() : bool
    {
        return \true;
    }
}
