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

use function sprintf;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Invocation;
use ECSPrefix20210803\SebastianBergmann\Exporter\Exporter;
use Throwable;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Exception implements \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\Stub
{
    private $exception;
    public function __construct(\Throwable $exception)
    {
        $this->exception = $exception;
    }
    /**
     * @throws Throwable
     */
    public function invoke(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Invocation $invocation) : void
    {
        throw $this->exception;
    }
    public function toString() : string
    {
        $exporter = new \ECSPrefix20210803\SebastianBergmann\Exporter\Exporter();
        return \sprintf('raise user-specified exception %s', $exporter->export($this->exception));
    }
}
