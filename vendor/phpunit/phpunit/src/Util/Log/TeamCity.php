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
namespace ECSPrefix20210803\PHPUnit\Util\Log;

use function class_exists;
use function count;
use function explode;
use function get_class;
use function getmypid;
use function ini_get;
use function is_bool;
use function is_scalar;
use function method_exists;
use function print_r;
use function round;
use function str_replace;
use function stripos;
use ECSPrefix20210803\PHPUnit\Framework\AssertionFailedError;
use ECSPrefix20210803\PHPUnit\Framework\ExceptionWrapper;
use ECSPrefix20210803\PHPUnit\Framework\ExpectationFailedException;
use ECSPrefix20210803\PHPUnit\Framework\Test;
use ECSPrefix20210803\PHPUnit\Framework\TestCase;
use ECSPrefix20210803\PHPUnit\Framework\TestFailure;
use ECSPrefix20210803\PHPUnit\Framework\TestResult;
use ECSPrefix20210803\PHPUnit\Framework\TestSuite;
use ECSPrefix20210803\PHPUnit\Framework\Warning;
use ECSPrefix20210803\PHPUnit\TextUI\DefaultResultPrinter;
use ECSPrefix20210803\PHPUnit\Util\Exception;
use ECSPrefix20210803\PHPUnit\Util\Filter;
use ReflectionClass;
use ReflectionException;
use ECSPrefix20210803\SebastianBergmann\Comparator\ComparisonFailure;
use Throwable;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TeamCity extends \ECSPrefix20210803\PHPUnit\TextUI\DefaultResultPrinter
{
    /**
     * @var bool
     */
    private $isSummaryTestCountPrinted = \false;
    /**
     * @var string
     */
    private $startedTestName;
    /**
     * @var false|int
     */
    private $flowId;
    public function printResult(\ECSPrefix20210803\PHPUnit\Framework\TestResult $result) : void
    {
        $this->printHeader($result);
        $this->printFooter($result);
    }
    /**
     * An error occurred.
     */
    public function addError(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->printEvent('testFailed', ['name' => $test->getName(), 'message' => self::getMessage($t), 'details' => self::getDetails($t), 'duration' => self::toMilliseconds($time)]);
    }
    /**
     * A warning occurred.
     */
    public function addWarning(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \ECSPrefix20210803\PHPUnit\Framework\Warning $e, float $time) : void
    {
        $this->write(self::getMessage($e) . \PHP_EOL);
    }
    /**
     * A failure occurred.
     */
    public function addFailure(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \ECSPrefix20210803\PHPUnit\Framework\AssertionFailedError $e, float $time) : void
    {
        $parameters = ['name' => $test->getName(), 'message' => self::getMessage($e), 'details' => self::getDetails($e), 'duration' => self::toMilliseconds($time)];
        if ($e instanceof \ECSPrefix20210803\PHPUnit\Framework\ExpectationFailedException) {
            $comparisonFailure = $e->getComparisonFailure();
            if ($comparisonFailure instanceof \ECSPrefix20210803\SebastianBergmann\Comparator\ComparisonFailure) {
                $expectedString = $comparisonFailure->getExpectedAsString();
                if ($expectedString === null || empty($expectedString)) {
                    $expectedString = self::getPrimitiveValueAsString($comparisonFailure->getExpected());
                }
                $actualString = $comparisonFailure->getActualAsString();
                if ($actualString === null || empty($actualString)) {
                    $actualString = self::getPrimitiveValueAsString($comparisonFailure->getActual());
                }
                if ($actualString !== null && $expectedString !== null) {
                    $parameters['type'] = 'comparisonFailure';
                    $parameters['actual'] = $actualString;
                    $parameters['expected'] = $expectedString;
                }
            }
        }
        $this->printEvent('testFailed', $parameters);
    }
    /**
     * Incomplete test.
     */
    public function addIncompleteTest(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->printIgnoredTest($test->getName(), $t, $time);
    }
    /**
     * Risky test.
     */
    public function addRiskyTest(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->addError($test, $t, $time);
    }
    /**
     * Skipped test.
     */
    public function addSkippedTest(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $testName = $test->getName();
        if ($this->startedTestName !== $testName) {
            $this->startTest($test);
            $this->printIgnoredTest($testName, $t, $time);
            $this->endTest($test, $time);
        } else {
            $this->printIgnoredTest($testName, $t, $time);
        }
    }
    public function printIgnoredTest(string $testName, \Throwable $t, float $time) : void
    {
        $this->printEvent('testIgnored', ['name' => $testName, 'message' => self::getMessage($t), 'details' => self::getDetails($t), 'duration' => self::toMilliseconds($time)]);
    }
    /**
     * A testsuite started.
     */
    public function startTestSuite(\ECSPrefix20210803\PHPUnit\Framework\TestSuite $suite) : void
    {
        if (\stripos(\ini_get('disable_functions'), 'getmypid') === \false) {
            $this->flowId = \getmypid();
        } else {
            $this->flowId = \false;
        }
        if (!$this->isSummaryTestCountPrinted) {
            $this->isSummaryTestCountPrinted = \true;
            $this->printEvent('testCount', ['count' => \count($suite)]);
        }
        $suiteName = $suite->getName();
        if (empty($suiteName)) {
            return;
        }
        $parameters = ['name' => $suiteName];
        if (\class_exists($suiteName, \false)) {
            $fileName = self::getFileName($suiteName);
            $parameters['locationHint'] = "php_qn://{$fileName}::\\{$suiteName}";
        } else {
            $split = \explode('::', $suiteName);
            if (\count($split) === 2 && \class_exists($split[0]) && \method_exists($split[0], $split[1])) {
                $fileName = self::getFileName($split[0]);
                $parameters['locationHint'] = "php_qn://{$fileName}::\\{$suiteName}";
                $parameters['name'] = $split[1];
            }
        }
        $this->printEvent('testSuiteStarted', $parameters);
    }
    /**
     * A testsuite ended.
     */
    public function endTestSuite(\ECSPrefix20210803\PHPUnit\Framework\TestSuite $suite) : void
    {
        $suiteName = $suite->getName();
        if (empty($suiteName)) {
            return;
        }
        $parameters = ['name' => $suiteName];
        if (!\class_exists($suiteName, \false)) {
            $split = \explode('::', $suiteName);
            if (\count($split) === 2 && \class_exists($split[0]) && \method_exists($split[0], $split[1])) {
                $parameters['name'] = $split[1];
            }
        }
        $this->printEvent('testSuiteFinished', $parameters);
    }
    /**
     * A test started.
     */
    public function startTest(\ECSPrefix20210803\PHPUnit\Framework\Test $test) : void
    {
        $testName = $test->getName();
        $this->startedTestName = $testName;
        $params = ['name' => $testName];
        if ($test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase) {
            $className = \get_class($test);
            $fileName = self::getFileName($className);
            $params['locationHint'] = "php_qn://{$fileName}::\\{$className}::{$testName}";
        }
        $this->printEvent('testStarted', $params);
    }
    /**
     * A test ended.
     */
    public function endTest(\ECSPrefix20210803\PHPUnit\Framework\Test $test, float $time) : void
    {
        parent::endTest($test, $time);
        $this->printEvent('testFinished', ['name' => $test->getName(), 'duration' => self::toMilliseconds($time)]);
    }
    protected function writeProgress(string $progress) : void
    {
    }
    private function printEvent(string $eventName, array $params = []) : void
    {
        $this->write("\n##teamcity[{$eventName}");
        if ($this->flowId) {
            $params['flowId'] = $this->flowId;
        }
        foreach ($params as $key => $value) {
            $escapedValue = self::escapeValue((string) $value);
            $this->write(" {$key}='{$escapedValue}'");
        }
        $this->write("]\n");
    }
    private static function getMessage(\Throwable $t) : string
    {
        $message = '';
        if ($t instanceof \ECSPrefix20210803\PHPUnit\Framework\ExceptionWrapper) {
            if ($t->getClassName() !== '') {
                $message .= $t->getClassName();
            }
            if ($message !== '' && $t->getMessage() !== '') {
                $message .= ' : ';
            }
        }
        return $message . $t->getMessage();
    }
    private static function getDetails(\Throwable $t) : string
    {
        $stackTrace = \ECSPrefix20210803\PHPUnit\Util\Filter::getFilteredStacktrace($t);
        $previous = $t instanceof \ECSPrefix20210803\PHPUnit\Framework\ExceptionWrapper ? $t->getPreviousWrapped() : $t->getPrevious();
        while ($previous) {
            $stackTrace .= "\nCaused by\n" . \ECSPrefix20210803\PHPUnit\Framework\TestFailure::exceptionToString($previous) . "\n" . \ECSPrefix20210803\PHPUnit\Util\Filter::getFilteredStacktrace($previous);
            $previous = $previous instanceof \ECSPrefix20210803\PHPUnit\Framework\ExceptionWrapper ? $previous->getPreviousWrapped() : $previous->getPrevious();
        }
        return ' ' . \str_replace("\n", "\n ", $stackTrace);
    }
    private static function getPrimitiveValueAsString($value) : ?string
    {
        if ($value === null) {
            return 'null';
        }
        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        if (\is_scalar($value)) {
            return \print_r($value, \true);
        }
        return null;
    }
    private static function escapeValue(string $text) : string
    {
        return \str_replace(['|', "'", "\n", "\r", ']', '['], ['||', "|'", '|n', '|r', '|]', '|['], $text);
    }
    /**
     * @param string $className
     */
    private static function getFileName($className) : string
    {
        try {
            return (new \ReflectionClass($className))->getFileName();
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new \ECSPrefix20210803\PHPUnit\Util\Exception($e->getMessage(), (int) $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
    }
    /**
     * @param float $time microseconds
     */
    private static function toMilliseconds(float $time) : int
    {
        return (int) \round($time * 1000);
    }
}
