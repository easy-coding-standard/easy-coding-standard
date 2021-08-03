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

use function explode;
use ECSPrefix20210803\PHPUnit\Util\Test as TestUtil;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class DataProviderTestSuite extends \ECSPrefix20210803\PHPUnit\Framework\TestSuite
{
    /**
     * @var list<ExecutionOrderDependency>
     */
    private $dependencies = [];
    /**
     * @param list<ExecutionOrderDependency> $dependencies
     */
    public function setDependencies(array $dependencies) : void
    {
        $this->dependencies = $dependencies;
        foreach ($this->tests as $test) {
            if (!$test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase) {
                // @codeCoverageIgnoreStart
                continue;
                // @codeCoverageIgnoreStart
            }
            $test->setDependencies($dependencies);
        }
    }
    /**
     * @return list<ExecutionOrderDependency>
     */
    public function provides() : array
    {
        if ($this->providedTests === null) {
            $this->providedTests = [new \ECSPrefix20210803\PHPUnit\Framework\ExecutionOrderDependency($this->getName())];
        }
        return $this->providedTests;
    }
    /**
     * @return list<ExecutionOrderDependency>
     */
    public function requires() : array
    {
        // A DataProviderTestSuite does not have to traverse its child tests
        // as these are inherited and cannot reference dataProvider rows directly
        return $this->dependencies;
    }
    /**
     * Returns the size of the each test created using the data provider(s).
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function getSize() : int
    {
        [$className, $methodName] = \explode('::', $this->getName());
        return \ECSPrefix20210803\PHPUnit\Util\Test::getSize($className, $methodName);
    }
}
