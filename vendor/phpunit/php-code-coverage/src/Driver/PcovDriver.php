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

use function extension_loaded;
use function phpversion;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\RawCodeCoverageData;
/**
 * @internal This class is not covered by the backward compatibility promise for phpunit/php-code-coverage
 */
final class PcovDriver extends \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Driver\Driver
{
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @throws PcovNotAvailableException
     */
    public function __construct(\ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter $filter)
    {
        if (!\extension_loaded('pcov')) {
            throw new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Driver\PcovNotAvailableException();
        }
        $this->filter = $filter;
    }
    public function start() : void
    {
        \pcov\start();
    }
    public function stop() : \ECSPrefix20210803\SebastianBergmann\CodeCoverage\RawCodeCoverageData
    {
        \pcov\stop();
        $collect = \pcov\collect(\pcov\inclusive, !$this->filter->isEmpty() ? $this->filter->files() : \pcov\waiting());
        \pcov\clear();
        return \ECSPrefix20210803\SebastianBergmann\CodeCoverage\RawCodeCoverageData::fromXdebugWithoutPathCoverage($collect);
    }
    public function nameAndVersion() : string
    {
        return 'PCOV ' . \phpversion('pcov');
    }
}
