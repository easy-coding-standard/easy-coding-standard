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
namespace ECSPrefix20210803\PHPUnit\Framework\MockObject\Builder;

use ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\Stub as BaseStub;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
interface Stub extends \ECSPrefix20210803\PHPUnit\Framework\MockObject\Builder\Identity
{
    /**
     * Stubs the matching method with the stub object $stub. Any invocations of
     * the matched method will now be handled by the stub instead.
     */
    public function will(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\Stub $stub) : \ECSPrefix20210803\PHPUnit\Framework\MockObject\Builder\Identity;
}
