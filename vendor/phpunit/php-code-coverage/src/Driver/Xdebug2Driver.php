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
namespace ECSPrefix20210803\SebastianBergmann\CodeCoverage\Driver;

use const XDEBUG_CC_BRANCH_CHECK;
use const XDEBUG_CC_DEAD_CODE;
use const XDEBUG_CC_UNUSED;
use const XDEBUG_FILTER_CODE_COVERAGE;
use const ECSPrefix20210803\XDEBUG_PATH_INCLUDE;
use const XDEBUG_PATH_WHITELIST;
use function defined;
use function extension_loaded;
use function ini_get;
use function phpversion;
use function sprintf;
use function version_compare;
use function xdebug_get_code_coverage;
use function xdebug_set_filter;
use function xdebug_start_code_coverage;
use function xdebug_stop_code_coverage;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\RawCodeCoverageData;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class Xdebug2Driver extends \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Driver\Driver
{
    /**
     * @var bool
     */
    private $pathCoverageIsMixedCoverage;
    /**
     * @throws XdebugNotAvailableException
     * @throws WrongXdebugVersionException
     * @throws Xdebug2NotEnabledException
     */
    public function __construct(\ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter $filter)
    {
        if (!\extension_loaded('xdebug')) {
            throw new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Driver\XdebugNotAvailableException();
        }
        if (\version_compare(\phpversion('xdebug'), '3', '>=')) {
            throw new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Driver\WrongXdebugVersionException(\sprintf('This driver requires Xdebug 2 but version %s is loaded', \phpversion('xdebug')));
        }
        if (!\ini_get('xdebug.coverage_enable')) {
            throw new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Driver\Xdebug2NotEnabledException();
        }
        if (!$filter->isEmpty()) {
            if (\defined('XDEBUG_PATH_WHITELIST')) {
                $listType = \XDEBUG_PATH_WHITELIST;
            } else {
                $listType = \XDEBUG_PATH_INCLUDE;
            }
            \xdebug_set_filter(\XDEBUG_FILTER_CODE_COVERAGE, $listType, $filter->files());
        }
        $this->pathCoverageIsMixedCoverage = \version_compare(\phpversion('xdebug'), '2.9.6', '<');
    }
    public function canCollectBranchAndPathCoverage() : bool
    {
        return \true;
    }
    public function canDetectDeadCode() : bool
    {
        return \true;
    }
    public function start() : void
    {
        $flags = \XDEBUG_CC_UNUSED;
        if ($this->detectsDeadCode() || $this->collectsBranchAndPathCoverage()) {
            $flags |= \XDEBUG_CC_DEAD_CODE;
        }
        if ($this->collectsBranchAndPathCoverage()) {
            $flags |= \XDEBUG_CC_BRANCH_CHECK;
        }
        \xdebug_start_code_coverage($flags);
    }
    public function stop() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\RawCodeCoverageData
    {
        $data = \xdebug_get_code_coverage();
        \xdebug_stop_code_coverage();
        if ($this->collectsBranchAndPathCoverage()) {
            if ($this->pathCoverageIsMixedCoverage) {
                return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\RawCodeCoverageData::fromXdebugWithMixedCoverage($data);
            }
            return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\RawCodeCoverageData::fromXdebugWithPathCoverage($data);
        }
        return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\RawCodeCoverageData::fromXdebugWithoutPathCoverage($data);
    }
    public function nameAndVersion() : string
    {
        return 'Xdebug ' . \phpversion('xdebug');
    }
}
