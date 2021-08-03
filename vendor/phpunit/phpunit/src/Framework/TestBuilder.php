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

use function assert;
use function count;
use function get_class;
use function sprintf;
use function trim;
use ECSPrefix20210803\PHPUnit\Util\Filter;
use ECSPrefix20210803\PHPUnit\Util\InvalidDataSetException;
use ECSPrefix20210803\PHPUnit\Util\Test as TestUtil;
use ReflectionClass;
use Throwable;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestBuilder
{
    public function build(\ReflectionClass $theClass, string $methodName) : \ECSPrefix20210803\PHPUnit\Framework\Test
    {
        $className = $theClass->getName();
        if (!$theClass->isInstantiable()) {
            return new \ECSPrefix20210803\PHPUnit\Framework\ErrorTestCase(\sprintf('Cannot instantiate class "%s".', $className));
        }
        $backupSettings = \ECSPrefix20210803\PHPUnit\Util\Test::getBackupSettings($className, $methodName);
        $preserveGlobalState = \ECSPrefix20210803\PHPUnit\Util\Test::getPreserveGlobalStateSettings($className, $methodName);
        $runTestInSeparateProcess = \ECSPrefix20210803\PHPUnit\Util\Test::getProcessIsolationSettings($className, $methodName);
        $runClassInSeparateProcess = \ECSPrefix20210803\PHPUnit\Util\Test::getClassProcessIsolationSettings($className, $methodName);
        $constructor = $theClass->getConstructor();
        if ($constructor === null) {
            throw new \ECSPrefix20210803\PHPUnit\Framework\Exception('No valid test provided.');
        }
        $parameters = $constructor->getParameters();
        // TestCase() or TestCase($name)
        if (\count($parameters) < 2) {
            $test = $this->buildTestWithoutData($className);
        } else {
            try {
                $data = \ECSPrefix20210803\PHPUnit\Util\Test::getProvidedData($className, $methodName);
            } catch (\ECSPrefix20210803\PHPUnit\Framework\IncompleteTestError $e) {
                $message = \sprintf("Test for %s::%s marked incomplete by data provider\n%s", $className, $methodName, $this->throwableToString($e));
                $data = new \ECSPrefix20210803\PHPUnit\Framework\IncompleteTestCase($className, $methodName, $message);
            } catch (\ECSPrefix20210803\PHPUnit\Framework\SkippedTestError $e) {
                $message = \sprintf("Test for %s::%s skipped by data provider\n%s", $className, $methodName, $this->throwableToString($e));
                $data = new \ECSPrefix20210803\PHPUnit\Framework\SkippedTestCase($className, $methodName, $message);
            } catch (\Throwable $t) {
                $message = \sprintf("The data provider specified for %s::%s is invalid.\n%s", $className, $methodName, $this->throwableToString($t));
                $data = new \ECSPrefix20210803\PHPUnit\Framework\ErrorTestCase($message);
            }
            // Test method with @dataProvider.
            if (isset($data)) {
                $test = $this->buildDataProviderTestSuite($methodName, $className, $data, $runTestInSeparateProcess, $preserveGlobalState, $runClassInSeparateProcess, $backupSettings);
            } else {
                $test = $this->buildTestWithoutData($className);
            }
        }
        if ($test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase) {
            $test->setName($methodName);
            $this->configureTestCase($test, $runTestInSeparateProcess, $preserveGlobalState, $runClassInSeparateProcess, $backupSettings);
        }
        return $test;
    }
    /** @psalm-param class-string $className */
    private function buildTestWithoutData(string $className)
    {
        return new $className();
    }
    /** @psalm-param class-string $className */
    private function buildDataProviderTestSuite(string $methodName, string $className, $data, bool $runTestInSeparateProcess, ?bool $preserveGlobalState, bool $runClassInSeparateProcess, array $backupSettings) : \ECSPrefix20210803\PHPUnit\Framework\DataProviderTestSuite
    {
        $dataProviderTestSuite = new \ECSPrefix20210803\PHPUnit\Framework\DataProviderTestSuite($className . '::' . $methodName);
        $groups = \ECSPrefix20210803\PHPUnit\Util\Test::getGroups($className, $methodName);
        if ($data instanceof \ECSPrefix20210803\PHPUnit\Framework\ErrorTestCase || $data instanceof \ECSPrefix20210803\PHPUnit\Framework\SkippedTestCase || $data instanceof \ECSPrefix20210803\PHPUnit\Framework\IncompleteTestCase) {
            $dataProviderTestSuite->addTest($data, $groups);
        } else {
            foreach ($data as $_dataName => $_data) {
                $_test = new $className($methodName, $_data, $_dataName);
                \assert($_test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase);
                $this->configureTestCase($_test, $runTestInSeparateProcess, $preserveGlobalState, $runClassInSeparateProcess, $backupSettings);
                $dataProviderTestSuite->addTest($_test, $groups);
            }
        }
        return $dataProviderTestSuite;
    }
    private function configureTestCase(\ECSPrefix20210803\PHPUnit\Framework\TestCase $test, bool $runTestInSeparateProcess, ?bool $preserveGlobalState, bool $runClassInSeparateProcess, array $backupSettings) : void
    {
        if ($runTestInSeparateProcess) {
            $test->setRunTestInSeparateProcess(\true);
            if ($preserveGlobalState !== null) {
                $test->setPreserveGlobalState($preserveGlobalState);
            }
        }
        if ($runClassInSeparateProcess) {
            $test->setRunClassInSeparateProcess(\true);
            if ($preserveGlobalState !== null) {
                $test->setPreserveGlobalState($preserveGlobalState);
            }
        }
        if ($backupSettings['backupGlobals'] !== null) {
            $test->setBackupGlobals($backupSettings['backupGlobals']);
        }
        if ($backupSettings['backupStaticAttributes'] !== null) {
            $test->setBackupStaticAttributes($backupSettings['backupStaticAttributes']);
        }
    }
    private function throwableToString(\Throwable $t) : string
    {
        $message = $t->getMessage();
        if (empty(\trim($message))) {
            $message = '<no message>';
        }
        if ($t instanceof \ECSPrefix20210803\PHPUnit\Util\InvalidDataSetException) {
            return \sprintf("%s\n%s", $message, \ECSPrefix20210803\PHPUnit\Util\Filter::getFilteredStacktrace($t));
        }
        return \sprintf("%s: %s\n%s", \get_class($t), $message, \ECSPrefix20210803\PHPUnit\Util\Filter::getFilteredStacktrace($t));
    }
}
