<?php

declare (strict_types=1);
/*
 * This file is part of phpunit/php-code-coverage.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver;

use function phpversion;
use function version_compare;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Filter;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
use ECSPrefix20210804\SebastianBergmann\Environment\Runtime;
final class Selector
{
    /**
     * @throws NoCodeCoverageDriverAvailableException
     * @throws PcovNotAvailableException
     * @throws PhpdbgNotAvailableException
     * @throws XdebugNotAvailableException
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
     */
    public function forLineCoverage(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\Filter $filter) : \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\Driver
    {
        $runtime = new \ECSPrefix20210804\SebastianBergmann\Environment\Runtime();
        if ($runtime->hasPHPDBGCodeCoverage()) {
            return new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\PhpdbgDriver();
        }
        if ($runtime->hasPCOV()) {
            return new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\PcovDriver($filter);
        }
        if ($runtime->hasXdebug()) {
            if (\version_compare(\phpversion('xdebug'), '3', '>=')) {
                $driver = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\Xdebug3Driver($filter);
            } else {
                $driver = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\Xdebug2Driver($filter);
            }
            $driver->enableDeadCodeDetection();
            return $driver;
        }
        throw new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException();
    }
    /**
     * @throws NoCodeCoverageDriverWithPathCoverageSupportAvailableException
     * @throws XdebugNotAvailableException
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
     */
    public function forLineAndPathCoverage(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\Filter $filter) : \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\Driver
    {
        if ((new \ECSPrefix20210804\SebastianBergmann\Environment\Runtime())->hasXdebug()) {
            if (\version_compare(\phpversion('xdebug'), '3', '>=')) {
                $driver = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\Xdebug3Driver($filter);
            } else {
                $driver = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\Xdebug2Driver($filter);
            }
            $driver->enableDeadCodeDetection();
            $driver->enableBranchAndPathCoverage();
            return $driver;
        }
        throw new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\NoCodeCoverageDriverWithPathCoverageSupportAvailableException();
    }
}
