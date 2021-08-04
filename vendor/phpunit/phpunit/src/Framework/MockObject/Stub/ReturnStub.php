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
namespace ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub;

use function sprintf;
use ECSPrefix20210804\PHPUnit\Framework\MockObject\Invocation;
use ECSPrefix20210804\SebastianBergmann\Exporter\Exporter;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ReturnStub implements \ECSPrefix20210804\PHPUnit\Framework\MockObject\Stub\Stub
{
    /**
     * @var mixed
     */
    private $value;
    public function __construct($value)
    {
        $this->value = $value;
    }
    public function invoke(\ECSPrefix20210804\PHPUnit\Framework\MockObject\Invocation $invocation)
    {
        return $this->value;
    }
    public function toString() : string
    {
        $exporter = new \ECSPrefix20210804\SebastianBergmann\Exporter\Exporter();
        return \sprintf('return user-specified value %s', $exporter->export($this->value));
    }
}
