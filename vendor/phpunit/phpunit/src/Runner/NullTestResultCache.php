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

/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class NullTestResultCache implements \ECSPrefix20210804\PHPUnit\Runner\TestResultCache
{
    public function setState(string $testName, int $state) : void
    {
    }
    public function getState(string $testName) : int
    {
        return \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_UNKNOWN;
    }
    public function setTime(string $testName, float $time) : void
    {
    }
    public function getTime(string $testName) : float
    {
        return 0;
    }
    public function load() : void
    {
    }
    public function persist() : void
    {
    }
}
