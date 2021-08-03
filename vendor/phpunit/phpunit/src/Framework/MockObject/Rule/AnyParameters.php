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
namespace ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule;

use ECSPrefix20210803\PHPUnit\Framework\MockObject\Invocation as BaseInvocation;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class AnyParameters implements \ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\ParametersRule
{
    public function toString() : string
    {
        return 'with any parameters';
    }
    public function apply(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Invocation $invocation) : void
    {
    }
    public function verify() : void
    {
    }
}
