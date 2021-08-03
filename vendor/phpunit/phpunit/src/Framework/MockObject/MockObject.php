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
namespace ECSPrefix20210803\PHPUnit\Framework\MockObject;

use ECSPrefix20210803\PHPUnit\Framework\MockObject\Builder\InvocationMocker as BuilderInvocationMocker;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\InvocationOrder;
/**
 * @method BuilderInvocationMocker method($constraint)
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
interface MockObject extends \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub
{
    public function __phpunit_setOriginalObject($originalObject) : void;
    public function __phpunit_verify(bool $unsetInvocationMocker = \true) : void;
    public function expects(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Rule\InvocationOrder $invocationRule) : \ECSPrefix20210803\PHPUnit\Framework\MockObject\Builder\InvocationMocker;
}
