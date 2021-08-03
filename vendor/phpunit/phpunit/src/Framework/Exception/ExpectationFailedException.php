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
namespace ECSPrefix20210803\PHPUnit\Framework;

use Exception;
use ECSPrefix20210803\SebastianBergmann\Comparator\ComparisonFailure;
/**
 * Exception for expectations which failed their check.
 *
 * The exception contains the error message and optionally a
 * SebastianBergmann\Comparator\ComparisonFailure which is used to
 * generate diff output of the failed expectations.
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class ExpectationFailedException extends \ECSPrefix20210803\PHPUnit\Framework\AssertionFailedError
{
    /**
     * @var ComparisonFailure
     */
    protected $comparisonFailure;
    public function __construct(string $message, \ECSPrefix20210803\SebastianBergmann\Comparator\ComparisonFailure $comparisonFailure = null, \Exception $previous = null)
    {
        $this->comparisonFailure = $comparisonFailure;
        parent::__construct($message, 0, $previous);
    }
    public function getComparisonFailure() : ?\ECSPrefix20210803\SebastianBergmann\Comparator\ComparisonFailure
    {
        return $this->comparisonFailure;
    }
}
