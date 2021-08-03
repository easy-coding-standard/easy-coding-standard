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

use function sprintf;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\BranchAndPathCoverageNotSupportedException;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\DeadCodeDetectionNotSupportedException;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\NoCodeCoverageDriverAvailableException;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\NoCodeCoverageDriverWithPathCoverageSupportAvailableException;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\RawCodeCoverageData;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
abstract class Driver
{
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const LINE_NOT_EXECUTABLE = -2;
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const LINE_NOT_EXECUTED = -1;
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const LINE_EXECUTED = 1;
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const BRANCH_NOT_HIT = 0;
    /**
     * @var int
     *
     * @see http://xdebug.org/docs/code_coverage
     */
    public const BRANCH_HIT = 1;
    /**
     * @var bool
     */
    private $collectBranchAndPathCoverage = \false;
    /**
     * @var bool
     */
    private $detectDeadCode = \false;
    /**
     * @throws NoCodeCoverageDriverAvailableException
     * @throws PcovNotAvailableException
     * @throws PhpdbgNotAvailableException
     * @throws XdebugNotAvailableException
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
     *
     * @deprecated Use DriverSelector::forLineCoverage() instead
     */
    public static function forLineCoverage(\ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter $filter) : self
    {
        return (new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Driver\Selector())->forLineCoverage($filter);
    }
    /**
     * @throws NoCodeCoverageDriverWithPathCoverageSupportAvailableException
     * @throws XdebugNotAvailableException
     * @throws Xdebug2NotEnabledException
     * @throws Xdebug3NotEnabledException
     *
     * @deprecated Use DriverSelector::forLineAndPathCoverage() instead
     */
    public static function forLineAndPathCoverage(\ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter $filter) : self
    {
        return (new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Driver\Selector())->forLineAndPathCoverage($filter);
    }
    public function canCollectBranchAndPathCoverage() : bool
    {
        return \false;
    }
    public function collectsBranchAndPathCoverage() : bool
    {
        return $this->collectBranchAndPathCoverage;
    }
    /**
     * @throws BranchAndPathCoverageNotSupportedException
     */
    public function enableBranchAndPathCoverage() : void
    {
        if (!$this->canCollectBranchAndPathCoverage()) {
            throw new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\BranchAndPathCoverageNotSupportedException(\sprintf('%s does not support branch and path coverage', $this->nameAndVersion()));
        }
        $this->collectBranchAndPathCoverage = \true;
    }
    public function disableBranchAndPathCoverage() : void
    {
        $this->collectBranchAndPathCoverage = \false;
    }
    public function canDetectDeadCode() : bool
    {
        return \false;
    }
    public function detectsDeadCode() : bool
    {
        return $this->detectDeadCode;
    }
    /**
     * @throws DeadCodeDetectionNotSupportedException
     */
    public function enableDeadCodeDetection() : void
    {
        if (!$this->canDetectDeadCode()) {
            throw new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\DeadCodeDetectionNotSupportedException(\sprintf('%s does not support dead code detection', $this->nameAndVersion()));
        }
        $this->detectDeadCode = \true;
    }
    public function disableDeadCodeDetection() : void
    {
        $this->detectDeadCode = \false;
    }
    public abstract function nameAndVersion() : string;
    public abstract function start() : void;
    public abstract function stop() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\RawCodeCoverageData;
}
