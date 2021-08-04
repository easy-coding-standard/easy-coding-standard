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
namespace ECSPrefix20210804\PHPUnit\Runner;

use ECSPrefix20210804\PHPUnit\Framework\AssertionFailedError;
use ECSPrefix20210804\PHPUnit\Framework\Test;
use ECSPrefix20210804\PHPUnit\Framework\TestListener;
use ECSPrefix20210804\PHPUnit\Framework\TestSuite;
use ECSPrefix20210804\PHPUnit\Framework\Warning;
use ECSPrefix20210804\PHPUnit\Util\Test as TestUtil;
use Throwable;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestListenerAdapter implements \ECSPrefix20210804\PHPUnit\Framework\TestListener
{
    /**
     * @var TestHook[]
     */
    private $hooks = [];
    /**
     * @var bool
     */
    private $lastTestWasNotSuccessful;
    public function add(\ECSPrefix20210804\PHPUnit\Runner\TestHook $hook) : void
    {
        $this->hooks[] = $hook;
    }
    public function startTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \ECSPrefix20210804\PHPUnit\Runner\BeforeTestHook) {
                $hook->executeBeforeTest(\ECSPrefix20210804\PHPUnit\Util\Test::describeAsString($test));
            }
        }
        $this->lastTestWasNotSuccessful = \false;
    }
    public function addError(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \ECSPrefix20210804\PHPUnit\Runner\AfterTestErrorHook) {
                $hook->executeAfterTestError(\ECSPrefix20210804\PHPUnit\Util\Test::describeAsString($test), $t->getMessage(), $time);
            }
        }
        $this->lastTestWasNotSuccessful = \true;
    }
    public function addWarning(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \ECSPrefix20210804\PHPUnit\Framework\Warning $e, float $time) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \ECSPrefix20210804\PHPUnit\Runner\AfterTestWarningHook) {
                $hook->executeAfterTestWarning(\ECSPrefix20210804\PHPUnit\Util\Test::describeAsString($test), $e->getMessage(), $time);
            }
        }
        $this->lastTestWasNotSuccessful = \true;
    }
    public function addFailure(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \ECSPrefix20210804\PHPUnit\Framework\AssertionFailedError $e, float $time) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \ECSPrefix20210804\PHPUnit\Runner\AfterTestFailureHook) {
                $hook->executeAfterTestFailure(\ECSPrefix20210804\PHPUnit\Util\Test::describeAsString($test), $e->getMessage(), $time);
            }
        }
        $this->lastTestWasNotSuccessful = \true;
    }
    public function addIncompleteTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \ECSPrefix20210804\PHPUnit\Runner\AfterIncompleteTestHook) {
                $hook->executeAfterIncompleteTest(\ECSPrefix20210804\PHPUnit\Util\Test::describeAsString($test), $t->getMessage(), $time);
            }
        }
        $this->lastTestWasNotSuccessful = \true;
    }
    public function addRiskyTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \ECSPrefix20210804\PHPUnit\Runner\AfterRiskyTestHook) {
                $hook->executeAfterRiskyTest(\ECSPrefix20210804\PHPUnit\Util\Test::describeAsString($test), $t->getMessage(), $time);
            }
        }
        $this->lastTestWasNotSuccessful = \true;
    }
    public function addSkippedTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \ECSPrefix20210804\PHPUnit\Runner\AfterSkippedTestHook) {
                $hook->executeAfterSkippedTest(\ECSPrefix20210804\PHPUnit\Util\Test::describeAsString($test), $t->getMessage(), $time);
            }
        }
        $this->lastTestWasNotSuccessful = \true;
    }
    public function endTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, float $time) : void
    {
        if (!$this->lastTestWasNotSuccessful) {
            foreach ($this->hooks as $hook) {
                if ($hook instanceof \ECSPrefix20210804\PHPUnit\Runner\AfterSuccessfulTestHook) {
                    $hook->executeAfterSuccessfulTest(\ECSPrefix20210804\PHPUnit\Util\Test::describeAsString($test), $time);
                }
            }
        }
        foreach ($this->hooks as $hook) {
            if ($hook instanceof \ECSPrefix20210804\PHPUnit\Runner\AfterTestHook) {
                $hook->executeAfterTest(\ECSPrefix20210804\PHPUnit\Util\Test::describeAsString($test), $time);
            }
        }
    }
    public function startTestSuite(\ECSPrefix20210804\PHPUnit\Framework\TestSuite $suite) : void
    {
    }
    public function endTestSuite(\ECSPrefix20210804\PHPUnit\Framework\TestSuite $suite) : void
    {
    }
}
