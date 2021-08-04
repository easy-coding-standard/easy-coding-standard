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
namespace ECSPrefix20210804\PHPUnit\Framework;

use Throwable;
/**
 * @deprecated The `TestListener` interface is deprecated
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
trait TestListenerDefaultImplementation
{
    public function addError(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
    }
    public function addWarning(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \ECSPrefix20210804\PHPUnit\Framework\Warning $e, float $time) : void
    {
    }
    public function addFailure(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \ECSPrefix20210804\PHPUnit\Framework\AssertionFailedError $e, float $time) : void
    {
    }
    public function addIncompleteTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
    }
    public function addRiskyTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
    }
    public function addSkippedTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
    }
    public function startTestSuite(\ECSPrefix20210804\PHPUnit\Framework\TestSuite $suite) : void
    {
    }
    public function endTestSuite(\ECSPrefix20210804\PHPUnit\Framework\TestSuite $suite) : void
    {
    }
    public function startTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test) : void
    {
    }
    public function endTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, float $time) : void
    {
    }
}
