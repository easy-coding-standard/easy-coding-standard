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
namespace ECSPrefix20210804\SebastianBergmann\CodeUnit;

/**
 * @psalm-immutable
 */
final class TraitUnit extends \ECSPrefix20210804\SebastianBergmann\CodeUnit\CodeUnit
{
    /**
     * @psalm-assert-if-true TraitUnit $this
     */
    public function isTrait() : bool
    {
        return \true;
    }
}
