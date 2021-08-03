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

use ECSPrefix20210803\PHPUnit\Framework\ExpectationFailedException;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Invocation as BaseInvocation;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Verifiable;
use ECSPrefix20210803\PHPUnit\Framework\SelfDescribing;
/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
interface ParametersRule extends \ECSPrefix20210803\PHPUnit\Framework\SelfDescribing, \ECSPrefix20210803\PHPUnit\Framework\MockObject\Verifiable
{
    /**
     * @throws ExpectationFailedException if the invocation violates the rule
     */
    public function apply(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Invocation $invocation) : void;
    public function verify() : void;
}
