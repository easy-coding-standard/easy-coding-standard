<?php

declare (strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\PHPUnit\Framework\Constraint;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
final class SameSize extends \ECSPrefix20210804\PHPUnit\Framework\Constraint\Count
{
    public function __construct(iterable $expected)
    {
        parent::__construct((int) $this->getCountOf($expected));
    }
}
