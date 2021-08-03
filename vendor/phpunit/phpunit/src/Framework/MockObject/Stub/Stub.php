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
namespace ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub;

use ECSPrefix20210803\PHPUnit\Framework\MockObject\Invocation;
use ECSPrefix20210803\PHPUnit\Framework\SelfDescribing;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
interface Stub extends \ECSPrefix20210803\PHPUnit\Framework\SelfDescribing
{
    /**
     * Fakes the processing of the invocation $invocation by returning a
     * specific value.
     *
     * @param Invocation $invocation The invocation which was mocked and matched by the current method and argument matchers
     */
    public function invoke(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Invocation $invocation);
}
