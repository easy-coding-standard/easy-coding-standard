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

use function array_shift;
use function sprintf;
use ECSPrefix20210803\PHPUnit\Framework\MockObject\Invocation;
use ECSPrefix20210803\SebastianBergmann\Exporter\Exporter;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ConsecutiveCalls implements \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\Stub
{
    /**
     * @var array
     */
    private $stack;
    /**
     * @var mixed
     */
    private $value;
    public function __construct(array $stack)
    {
        $this->stack = $stack;
    }
    public function invoke(\ECSPrefix20210803\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        $this->value = \array_shift($this->stack);
        if ($this->value instanceof \ECSPrefix20210803\PHPUnit\Framework\MockObject\Stub\Stub) {
            $this->value = $this->value->invoke($invocation);
        }
        return $this->value;
    }
    public function toString() : string
    {
        $exporter = new \ECSPrefix20210803\SebastianBergmann\Exporter\Exporter();
        return \sprintf('return user-specified value %s', $exporter->export($this->value));
    }
}
