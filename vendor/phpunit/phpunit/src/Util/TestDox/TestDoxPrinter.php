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
namespace ECSPrefix20210804\PHPUnit\Util\TestDox;

use const PHP_EOL;
use function array_map;
use function get_class;
use function implode;
use function method_exists;
use function preg_split;
use function trim;
use ECSPrefix20210804\PHPUnit\Framework\AssertionFailedError;
use ECSPrefix20210804\PHPUnit\Framework\Reorderable;
use ECSPrefix20210804\PHPUnit\Framework\Test;
use ECSPrefix20210804\PHPUnit\Framework\TestCase;
use ECSPrefix20210804\PHPUnit\Framework\TestResult;
use ECSPrefix20210804\PHPUnit\Framework\TestSuite;
use ECSPrefix20210804\PHPUnit\Framework\Warning;
use ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner;
use ECSPrefix20210804\PHPUnit\Runner\PhptTestCase;
use ECSPrefix20210804\PHPUnit\TextUI\DefaultResultPrinter;
use Throwable;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
class TestDoxPrinter extends \ECSPrefix20210804\PHPUnit\TextUI\DefaultResultPrinter
{
    /**
     * @var NamePrettifier
     */
    protected $prettifier;
    /**
     * @var int The number of test results received from the TestRunner
     */
    protected $testIndex = 0;
    /**
     * @var int The number of test results already sent to the output
     */
    protected $testFlushIndex = 0;
    /**
     * @var array<int, array> Buffer for test results
     */
    protected $testResults = [];
    /**
     * @var array<string, int> Lookup table for testname to testResults[index]
     */
    protected $testNameResultIndex = [];
    /**
     * @var bool
     */
    protected $enableOutputBuffer = \false;
    /**
     * @var array array<string>
     */
    protected $originalExecutionOrder = [];
    /**
     * @var int
     */
    protected $spinState = 0;
    /**
     * @var bool
     */
    protected $showProgress = \true;
    /**
     * @param null|resource|string $out
     * @param int|string           $numberOfColumns
     *
     * @throws \PHPUnit\Framework\Exception
     */
    public function __construct($out = null, bool $verbose = \false, string $colors = self::COLOR_DEFAULT, bool $debug = \false, $numberOfColumns = 80, bool $reverse = \false)
    {
        parent::__construct($out, $verbose, $colors, $debug, $numberOfColumns, $reverse);
        $this->prettifier = new \ECSPrefix20210804\PHPUnit\Util\TestDox\NamePrettifier($this->colors);
    }
    public function setOriginalExecutionOrder(array $order) : void
    {
        $this->originalExecutionOrder = $order;
        $this->enableOutputBuffer = !empty($order);
    }
    public function setShowProgressAnimation(bool $showProgress) : void
    {
        $this->showProgress = $showProgress;
    }
    public function printResult(\ECSPrefix20210804\PHPUnit\Framework\TestResult $result) : void
    {
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function endTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, float $time) : void
    {
        if (!$test instanceof \ECSPrefix20210804\PHPUnit\Framework\TestCase && !$test instanceof \ECSPrefix20210804\PHPUnit\Runner\PhptTestCase && !$test instanceof \ECSPrefix20210804\PHPUnit\Framework\TestSuite) {
            return;
        }
        if ($this->testHasPassed()) {
            $this->registerTestResult($test, null, \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_PASSED, $time, \false);
        }
        if ($test instanceof \ECSPrefix20210804\PHPUnit\Framework\TestCase || $test instanceof \ECSPrefix20210804\PHPUnit\Runner\PhptTestCase) {
            $this->testIndex++;
        }
        parent::endTest($test, $time);
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function addError(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->registerTestResult($test, $t, \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_ERROR, $time, \true);
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function addWarning(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \ECSPrefix20210804\PHPUnit\Framework\Warning $e, float $time) : void
    {
        $this->registerTestResult($test, $e, \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_WARNING, $time, \true);
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function addFailure(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \ECSPrefix20210804\PHPUnit\Framework\AssertionFailedError $e, float $time) : void
    {
        $this->registerTestResult($test, $e, \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_FAILURE, $time, \true);
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function addIncompleteTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->registerTestResult($test, $t, \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_INCOMPLETE, $time, \false);
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function addRiskyTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->registerTestResult($test, $t, \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_RISKY, $time, \false);
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function addSkippedTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        $this->registerTestResult($test, $t, \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_SKIPPED, $time, \false);
    }
    public function writeProgress(string $progress) : void
    {
        $this->flushOutputBuffer();
    }
    public function flush() : void
    {
        $this->flushOutputBuffer(\true);
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    protected function registerTestResult(\ECSPrefix20210804\PHPUnit\Framework\Test $test, ?\Throwable $t, int $status, float $time, bool $verbose) : void
    {
        $testName = $test instanceof \ECSPrefix20210804\PHPUnit\Framework\Reorderable ? $test->sortId() : $test->getName();
        $result = ['className' => $this->formatClassName($test), 'testName' => $testName, 'testMethod' => $this->formatTestName($test), 'message' => '', 'status' => $status, 'time' => $time, 'verbose' => $verbose];
        if ($t !== null) {
            $result['message'] = $this->formatTestResultMessage($t, $result);
        }
        $this->testResults[$this->testIndex] = $result;
        $this->testNameResultIndex[$testName] = $this->testIndex;
    }
    protected function formatTestName(\ECSPrefix20210804\PHPUnit\Framework\Test $test) : string
    {
        return \method_exists($test, 'getName') ? $test->getName() : '';
    }
    protected function formatClassName(\ECSPrefix20210804\PHPUnit\Framework\Test $test) : string
    {
        return \get_class($test);
    }
    protected function testHasPassed() : bool
    {
        if (!isset($this->testResults[$this->testIndex]['status'])) {
            return \true;
        }
        if ($this->testResults[$this->testIndex]['status'] === \ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::STATUS_PASSED) {
            return \true;
        }
        return \false;
    }
    protected function flushOutputBuffer(bool $forceFlush = \false) : void
    {
        if ($this->testFlushIndex === $this->testIndex) {
            return;
        }
        if ($this->testFlushIndex > 0) {
            if ($this->enableOutputBuffer && isset($this->originalExecutionOrder[$this->testFlushIndex - 1])) {
                $prevResult = $this->getTestResultByName($this->originalExecutionOrder[$this->testFlushIndex - 1]);
            } else {
                $prevResult = $this->testResults[$this->testFlushIndex - 1];
            }
        } else {
            $prevResult = $this->getEmptyTestResult();
        }
        if (!$this->enableOutputBuffer) {
            $this->writeTestResult($prevResult, $this->testResults[$this->testFlushIndex++]);
        } else {
            do {
                $flushed = \false;
                if (!$forceFlush && isset($this->originalExecutionOrder[$this->testFlushIndex])) {
                    $result = $this->getTestResultByName($this->originalExecutionOrder[$this->testFlushIndex]);
                } else {
                    // This test(name) cannot found in original execution order,
                    // flush result to output stream right away
                    $result = $this->testResults[$this->testFlushIndex];
                }
                if (!empty($result)) {
                    $this->hideSpinner();
                    $this->writeTestResult($prevResult, $result);
                    $this->testFlushIndex++;
                    $prevResult = $result;
                    $flushed = \true;
                } else {
                    $this->showSpinner();
                }
            } while ($flushed && $this->testFlushIndex < $this->testIndex);
        }
    }
    protected function showSpinner() : void
    {
        if (!$this->showProgress) {
            return;
        }
        if ($this->spinState) {
            $this->undrawSpinner();
        }
        $this->spinState++;
        $this->drawSpinner();
    }
    protected function hideSpinner() : void
    {
        if (!$this->showProgress) {
            return;
        }
        if ($this->spinState) {
            $this->undrawSpinner();
        }
        $this->spinState = 0;
    }
    protected function drawSpinner() : void
    {
        // optional for CLI printers: show the user a 'buffering output' spinner
    }
    protected function undrawSpinner() : void
    {
        // remove the spinner from the current line
    }
    protected function writeTestResult(array $prevResult, array $result) : void
    {
    }
    protected function getEmptyTestResult() : array
    {
        return ['className' => '', 'testName' => '', 'message' => '', 'failed' => '', 'verbose' => ''];
    }
    protected function getTestResultByName(?string $testName) : array
    {
        if (isset($this->testNameResultIndex[$testName])) {
            return $this->testResults[$this->testNameResultIndex[$testName]];
        }
        return [];
    }
    protected function formatThrowable(\Throwable $t, ?int $status = null) : string
    {
        $message = \trim(\ECSPrefix20210804\PHPUnit\Framework\TestFailure::exceptionToString($t));
        if ($message) {
            $message .= \PHP_EOL . \PHP_EOL . $this->formatStacktrace($t);
        } else {
            $message = $this->formatStacktrace($t);
        }
        return $message;
    }
    protected function formatStacktrace(\Throwable $t) : string
    {
        return \ECSPrefix20210804\PHPUnit\Util\Filter::getFilteredStacktrace($t);
    }
    protected function formatTestResultMessage(\Throwable $t, array $result, string $prefix = '│') : string
    {
        $message = $this->formatThrowable($t, $result['status']);
        if ($message === '') {
            return '';
        }
        if (!($this->verbose || $result['verbose'])) {
            return '';
        }
        return $this->prefixLines($prefix, $message);
    }
    protected function prefixLines(string $prefix, string $message) : string
    {
        $message = \trim($message);
        return \implode(\PHP_EOL, \array_map(static function (string $text) use($prefix) {
            return '   ' . $prefix . ($text ? ' ' . $text : '');
        }, \preg_split('/\\r\\n|\\r|\\n/', $message)));
    }
}
