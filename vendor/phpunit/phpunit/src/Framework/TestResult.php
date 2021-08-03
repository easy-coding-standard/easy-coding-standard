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

use const PHP_EOL;
use function count;
use function function_exists;
use function get_class;
use function sprintf;
use AssertionError;
use Countable;
use Error;
use ECSPrefix20210803\PHPUnit\Util\ErrorHandler;
use ECSPrefix20210803\PHPUnit\Util\ExcludeList;
use ECSPrefix20210803\PHPUnit\Util\Printer;
use ECSPrefix20210803\PHPUnit\Util\Test as TestUtil;
use ReflectionClass;
use ReflectionException;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\CodeCoverage;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\Exception as OriginalCodeCoverageException;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\UnintentionallyCoveredCodeException;
use ECSPrefix20210803\SebastianBergmann\Invoker\Invoker;
use ECSPrefix20210803\SebastianBergmann\Invoker\TimeoutException;
use ECSPrefix20210803\SebastianBergmann\ResourceOperations\ResourceOperations;
use ECSPrefix20210803\SebastianBergmann\Timer\Timer;
use Throwable;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class TestResult implements \Countable
{
    /**
     * @var array
     */
    private $passed = [];
    /**
     * @var array<string>
     */
    private $passedTestClasses = [];
    /**
     * @var bool
     */
    private $currentTestSuiteFailed = \false;
    /**
     * @var TestFailure[]
     */
    private $errors = [];
    /**
     * @var TestFailure[]
     */
    private $failures = [];
    /**
     * @var TestFailure[]
     */
    private $warnings = [];
    /**
     * @var TestFailure[]
     */
    private $notImplemented = [];
    /**
     * @var TestFailure[]
     */
    private $risky = [];
    /**
     * @var TestFailure[]
     */
    private $skipped = [];
    /**
     * @deprecated Use the `TestHook` interfaces instead
     *
     * @var TestListener[]
     */
    private $listeners = [];
    /**
     * @var int
     */
    private $runTests = 0;
    /**
     * @var float
     */
    private $time = 0;
    /**
     * Code Coverage information.
     *
     * @var CodeCoverage
     */
    private $codeCoverage;
    /**
     * @var bool
     */
    private $convertDeprecationsToExceptions = \true;
    /**
     * @var bool
     */
    private $convertErrorsToExceptions = \true;
    /**
     * @var bool
     */
    private $convertNoticesToExceptions = \true;
    /**
     * @var bool
     */
    private $convertWarningsToExceptions = \true;
    /**
     * @var bool
     */
    private $stop = \false;
    /**
     * @var bool
     */
    private $stopOnError = \false;
    /**
     * @var bool
     */
    private $stopOnFailure = \false;
    /**
     * @var bool
     */
    private $stopOnWarning = \false;
    /**
     * @var bool
     */
    private $beStrictAboutTestsThatDoNotTestAnything = \true;
    /**
     * @var bool
     */
    private $beStrictAboutOutputDuringTests = \false;
    /**
     * @var bool
     */
    private $beStrictAboutTodoAnnotatedTests = \false;
    /**
     * @var bool
     */
    private $beStrictAboutResourceUsageDuringSmallTests = \false;
    /**
     * @var bool
     */
    private $enforceTimeLimit = \false;
    /**
     * @var bool
     */
    private $forceCoversAnnotation = \false;
    /**
     * @var int
     */
    private $timeoutForSmallTests = 1;
    /**
     * @var int
     */
    private $timeoutForMediumTests = 10;
    /**
     * @var int
     */
    private $timeoutForLargeTests = 60;
    /**
     * @var bool
     */
    private $stopOnRisky = \false;
    /**
     * @var bool
     */
    private $stopOnIncomplete = \false;
    /**
     * @var bool
     */
    private $stopOnSkipped = \false;
    /**
     * @var bool
     */
    private $lastTestFailed = \false;
    /**
     * @var int
     */
    private $defaultTimeLimit = 0;
    /**
     * @var bool
     */
    private $stopOnDefect = \false;
    /**
     * @var bool
     */
    private $registerMockObjectsFromTestArgumentsRecursively = \false;
    /**
     * @deprecated Use the `TestHook` interfaces instead
     *
     * @codeCoverageIgnore
     *
     * Registers a TestListener.
     */
    public function addListener(\ECSPrefix20210803\PHPUnit\Framework\TestListener $listener) : void
    {
        $this->listeners[] = $listener;
    }
    /**
     * @deprecated Use the `TestHook` interfaces instead
     *
     * @codeCoverageIgnore
     *
     * Unregisters a TestListener.
     */
    public function removeListener(\ECSPrefix20210803\PHPUnit\Framework\TestListener $listener) : void
    {
        foreach ($this->listeners as $key => $_listener) {
            if ($listener === $_listener) {
                unset($this->listeners[$key]);
            }
        }
    }
    /**
     * @deprecated Use the `TestHook` interfaces instead
     *
     * @codeCoverageIgnore
     *
     * Flushes all flushable TestListeners.
     */
    public function flushListeners() : void
    {
        foreach ($this->listeners as $listener) {
            if ($listener instanceof \ECSPrefix20210803\PHPUnit\Util\Printer) {
                $listener->flush();
            }
        }
    }
    /**
     * Adds an error to the list of errors.
     */
    public function addError(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t, float $time) : void
    {
        if ($t instanceof \ECSPrefix20210803\PHPUnit\Framework\RiskyTestError) {
            $this->recordRisky($test, $t);
            $notifyMethod = 'addRiskyTest';
            if ($test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase) {
                $test->markAsRisky();
            }
            if ($this->stopOnRisky || $this->stopOnDefect) {
                $this->stop();
            }
        } elseif ($t instanceof \ECSPrefix20210803\PHPUnit\Framework\IncompleteTest) {
            $this->recordNotImplemented($test, $t);
            $notifyMethod = 'addIncompleteTest';
            if ($this->stopOnIncomplete) {
                $this->stop();
            }
        } elseif ($t instanceof \ECSPrefix20210803\PHPUnit\Framework\SkippedTest) {
            $this->recordSkipped($test, $t);
            $notifyMethod = 'addSkippedTest';
            if ($this->stopOnSkipped) {
                $this->stop();
            }
        } else {
            $this->recordError($test, $t);
            $notifyMethod = 'addError';
            if ($this->stopOnError || $this->stopOnFailure) {
                $this->stop();
            }
        }
        // @see https://github.com/sebastianbergmann/phpunit/issues/1953
        if ($t instanceof \Error) {
            $t = new \ECSPrefix20210803\PHPUnit\Framework\ExceptionWrapper($t);
        }
        foreach ($this->listeners as $listener) {
            $listener->{$notifyMethod}($test, $t, $time);
        }
        $this->lastTestFailed = \true;
        $this->time += $time;
    }
    /**
     * Adds a warning to the list of warnings.
     * The passed in exception caused the warning.
     */
    public function addWarning(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \ECSPrefix20210803\PHPUnit\Framework\Warning $e, float $time) : void
    {
        if ($this->stopOnWarning || $this->stopOnDefect) {
            $this->stop();
        }
        $this->recordWarning($test, $e);
        foreach ($this->listeners as $listener) {
            $listener->addWarning($test, $e, $time);
        }
        $this->time += $time;
    }
    /**
     * Adds a failure to the list of failures.
     * The passed in exception caused the failure.
     */
    public function addFailure(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \ECSPrefix20210803\PHPUnit\Framework\AssertionFailedError $e, float $time) : void
    {
        if ($e instanceof \ECSPrefix20210803\PHPUnit\Framework\RiskyTestError || $e instanceof \ECSPrefix20210803\PHPUnit\Framework\OutputError) {
            $this->recordRisky($test, $e);
            $notifyMethod = 'addRiskyTest';
            if ($test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase) {
                $test->markAsRisky();
            }
            if ($this->stopOnRisky || $this->stopOnDefect) {
                $this->stop();
            }
        } elseif ($e instanceof \ECSPrefix20210803\PHPUnit\Framework\IncompleteTest) {
            $this->recordNotImplemented($test, $e);
            $notifyMethod = 'addIncompleteTest';
            if ($this->stopOnIncomplete) {
                $this->stop();
            }
        } elseif ($e instanceof \ECSPrefix20210803\PHPUnit\Framework\SkippedTest) {
            $this->recordSkipped($test, $e);
            $notifyMethod = 'addSkippedTest';
            if ($this->stopOnSkipped) {
                $this->stop();
            }
        } else {
            $this->failures[] = new \ECSPrefix20210803\PHPUnit\Framework\TestFailure($test, $e);
            $notifyMethod = 'addFailure';
            if ($this->stopOnFailure || $this->stopOnDefect) {
                $this->stop();
            }
        }
        foreach ($this->listeners as $listener) {
            $listener->{$notifyMethod}($test, $e, $time);
        }
        $this->lastTestFailed = \true;
        $this->time += $time;
    }
    /**
     * Informs the result that a test suite will be started.
     */
    public function startTestSuite(\ECSPrefix20210803\PHPUnit\Framework\TestSuite $suite) : void
    {
        $this->currentTestSuiteFailed = \false;
        foreach ($this->listeners as $listener) {
            $listener->startTestSuite($suite);
        }
    }
    /**
     * Informs the result that a test suite was completed.
     */
    public function endTestSuite(\ECSPrefix20210803\PHPUnit\Framework\TestSuite $suite) : void
    {
        if (!$this->currentTestSuiteFailed) {
            $this->passedTestClasses[] = $suite->getName();
        }
        foreach ($this->listeners as $listener) {
            $listener->endTestSuite($suite);
        }
    }
    /**
     * Informs the result that a test will be started.
     */
    public function startTest(\ECSPrefix20210803\PHPUnit\Framework\Test $test) : void
    {
        $this->lastTestFailed = \false;
        $this->runTests += \count($test);
        foreach ($this->listeners as $listener) {
            $listener->startTest($test);
        }
    }
    /**
     * Informs the result that a test was completed.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function endTest(\ECSPrefix20210803\PHPUnit\Framework\Test $test, float $time) : void
    {
        foreach ($this->listeners as $listener) {
            $listener->endTest($test, $time);
        }
        if (!$this->lastTestFailed && $test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase) {
            $class = \get_class($test);
            $key = $class . '::' . $test->getName();
            $this->passed[$key] = ['result' => $test->getResult(), 'size' => \ECSPrefix20210803\PHPUnit\Util\Test::getSize($class, $test->getName(\false))];
            $this->time += $time;
        }
        if ($this->lastTestFailed && $test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase) {
            $this->currentTestSuiteFailed = \true;
        }
    }
    /**
     * Returns true if no risky test occurred.
     */
    public function allHarmless() : bool
    {
        return $this->riskyCount() === 0;
    }
    /**
     * Gets the number of risky tests.
     */
    public function riskyCount() : int
    {
        return \count($this->risky);
    }
    /**
     * Returns true if no incomplete test occurred.
     */
    public function allCompletelyImplemented() : bool
    {
        return $this->notImplementedCount() === 0;
    }
    /**
     * Gets the number of incomplete tests.
     */
    public function notImplementedCount() : int
    {
        return \count($this->notImplemented);
    }
    /**
     * Returns an array of TestFailure objects for the risky tests.
     *
     * @return TestFailure[]
     */
    public function risky() : array
    {
        return $this->risky;
    }
    /**
     * Returns an array of TestFailure objects for the incomplete tests.
     *
     * @return TestFailure[]
     */
    public function notImplemented() : array
    {
        return $this->notImplemented;
    }
    /**
     * Returns true if no test has been skipped.
     */
    public function noneSkipped() : bool
    {
        return $this->skippedCount() === 0;
    }
    /**
     * Gets the number of skipped tests.
     */
    public function skippedCount() : int
    {
        return \count($this->skipped);
    }
    /**
     * Returns an array of TestFailure objects for the skipped tests.
     *
     * @return TestFailure[]
     */
    public function skipped() : array
    {
        return $this->skipped;
    }
    /**
     * Gets the number of detected errors.
     */
    public function errorCount() : int
    {
        return \count($this->errors);
    }
    /**
     * Returns an array of TestFailure objects for the errors.
     *
     * @return TestFailure[]
     */
    public function errors() : array
    {
        return $this->errors;
    }
    /**
     * Gets the number of detected failures.
     */
    public function failureCount() : int
    {
        return \count($this->failures);
    }
    /**
     * Returns an array of TestFailure objects for the failures.
     *
     * @return TestFailure[]
     */
    public function failures() : array
    {
        return $this->failures;
    }
    /**
     * Gets the number of detected warnings.
     */
    public function warningCount() : int
    {
        return \count($this->warnings);
    }
    /**
     * Returns an array of TestFailure objects for the warnings.
     *
     * @return TestFailure[]
     */
    public function warnings() : array
    {
        return $this->warnings;
    }
    /**
     * Returns the names of the tests that have passed.
     */
    public function passed() : array
    {
        return $this->passed;
    }
    /**
     * Returns the names of the TestSuites that have passed.
     *
     * This enables @depends-annotations for TestClassName::class
     */
    public function passedClasses() : array
    {
        return $this->passedTestClasses;
    }
    /**
     * Returns whether code coverage information should be collected.
     */
    public function getCollectCodeCoverageInformation() : bool
    {
        return $this->codeCoverage !== null;
    }
    /**
     * Runs a TestCase.
     *
     * @throws \SebastianBergmann\CodeCoverage\InvalidArgumentException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws CodeCoverageException
     * @throws UnintentionallyCoveredCodeException
     */
    public function run(\ECSPrefix20210803\PHPUnit\Framework\Test $test) : void
    {
        \ECSPrefix20210803\PHPUnit\Framework\Assert::resetCount();
        if ($test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase) {
            $test->setRegisterMockObjectsFromTestArgumentsRecursively($this->registerMockObjectsFromTestArgumentsRecursively);
            $isAnyCoverageRequired = \ECSPrefix20210803\PHPUnit\Util\Test::requiresCodeCoverageDataCollection($test);
        }
        $error = \false;
        $failure = \false;
        $warning = \false;
        $incomplete = \false;
        $risky = \false;
        $skipped = \false;
        $this->startTest($test);
        if ($this->convertDeprecationsToExceptions || $this->convertErrorsToExceptions || $this->convertNoticesToExceptions || $this->convertWarningsToExceptions) {
            $errorHandler = new \ECSPrefix20210803\PHPUnit\Util\ErrorHandler($this->convertDeprecationsToExceptions, $this->convertErrorsToExceptions, $this->convertNoticesToExceptions, $this->convertWarningsToExceptions);
            $errorHandler->register();
        }
        $collectCodeCoverage = $this->codeCoverage !== null && !$test instanceof \ECSPrefix20210803\PHPUnit\Framework\ErrorTestCase && !$test instanceof \ECSPrefix20210803\PHPUnit\Framework\WarningTestCase && $isAnyCoverageRequired;
        if ($collectCodeCoverage) {
            $this->codeCoverage->start($test);
        }
        $monitorFunctions = $this->beStrictAboutResourceUsageDuringSmallTests && !$test instanceof \ECSPrefix20210803\PHPUnit\Framework\ErrorTestCase && !$test instanceof \ECSPrefix20210803\PHPUnit\Framework\WarningTestCase && $test->getSize() === \ECSPrefix20210803\PHPUnit\Util\Test::SMALL && \function_exists('xdebug_start_function_monitor');
        if ($monitorFunctions) {
            /* @noinspection ForgottenDebugOutputInspection */
            \xdebug_start_function_monitor(\ECSPrefix20210803\SebastianBergmann\ResourceOperations\ResourceOperations::getFunctions());
        }
        $timer = new \ECSPrefix20210803\SebastianBergmann\Timer\Timer();
        $timer->start();
        try {
            $invoker = new \ECSPrefix20210803\SebastianBergmann\Invoker\Invoker();
            if (!$test instanceof \ECSPrefix20210803\PHPUnit\Framework\ErrorTestCase && !$test instanceof \ECSPrefix20210803\PHPUnit\Framework\WarningTestCase && $this->enforceTimeLimit && ($this->defaultTimeLimit || $test->getSize() != \ECSPrefix20210803\PHPUnit\Util\Test::UNKNOWN) && $invoker->canInvokeWithTimeout()) {
                switch ($test->getSize()) {
                    case \ECSPrefix20210803\PHPUnit\Util\Test::SMALL:
                        $_timeout = $this->timeoutForSmallTests;
                        break;
                    case \ECSPrefix20210803\PHPUnit\Util\Test::MEDIUM:
                        $_timeout = $this->timeoutForMediumTests;
                        break;
                    case \ECSPrefix20210803\PHPUnit\Util\Test::LARGE:
                        $_timeout = $this->timeoutForLargeTests;
                        break;
                    case \ECSPrefix20210803\PHPUnit\Util\Test::UNKNOWN:
                        $_timeout = $this->defaultTimeLimit;
                        break;
                }
                $invoker->invoke([$test, 'runBare'], [], $_timeout);
            } else {
                $test->runBare();
            }
        } catch (\ECSPrefix20210803\SebastianBergmann\Invoker\TimeoutException $e) {
            $this->addFailure($test, new \ECSPrefix20210803\PHPUnit\Framework\RiskyTestError($e->getMessage()), $_timeout);
            $risky = \true;
        } catch (\ECSPrefix20210803\PHPUnit\Framework\AssertionFailedError $e) {
            $failure = \true;
            if ($e instanceof \ECSPrefix20210803\PHPUnit\Framework\RiskyTestError) {
                $risky = \true;
            } elseif ($e instanceof \ECSPrefix20210803\PHPUnit\Framework\IncompleteTestError) {
                $incomplete = \true;
            } elseif ($e instanceof \ECSPrefix20210803\PHPUnit\Framework\SkippedTestError) {
                $skipped = \true;
            }
        } catch (\AssertionError $e) {
            $test->addToAssertionCount(1);
            $failure = \true;
            $frame = $e->getTrace()[0];
            $e = new \ECSPrefix20210803\PHPUnit\Framework\AssertionFailedError(\sprintf('%s in %s:%s', $e->getMessage(), $frame['file'], $frame['line']));
        } catch (\ECSPrefix20210803\PHPUnit\Framework\Warning $e) {
            $warning = \true;
        } catch (\ECSPrefix20210803\PHPUnit\Framework\Exception $e) {
            $error = \true;
        } catch (\Throwable $e) {
            $e = new \ECSPrefix20210803\PHPUnit\Framework\ExceptionWrapper($e);
            $error = \true;
        }
        $time = $timer->stop()->asSeconds();
        $test->addToAssertionCount(\ECSPrefix20210803\PHPUnit\Framework\Assert::getCount());
        if ($monitorFunctions) {
            $excludeList = new \ECSPrefix20210803\PHPUnit\Util\ExcludeList();
            /** @noinspection ForgottenDebugOutputInspection */
            $functions = \xdebug_get_monitored_functions();
            /* @noinspection ForgottenDebugOutputInspection */
            \xdebug_stop_function_monitor();
            foreach ($functions as $function) {
                if (!$excludeList->isExcluded($function['filename'])) {
                    $this->addFailure($test, new \ECSPrefix20210803\PHPUnit\Framework\RiskyTestError(\sprintf('%s() used in %s:%s', $function['function'], $function['filename'], $function['lineno'])), $time);
                }
            }
        }
        if ($this->beStrictAboutTestsThatDoNotTestAnything && $test->getNumAssertions() === 0) {
            $risky = \true;
        }
        if ($this->forceCoversAnnotation && !$error && !$failure && !$warning && !$incomplete && !$skipped && !$risky) {
            $annotations = \ECSPrefix20210803\PHPUnit\Util\Test::parseTestMethodAnnotations(\get_class($test), $test->getName(\false));
            if (!isset($annotations['class']['covers']) && !isset($annotations['method']['covers']) && !isset($annotations['class']['coversNothing']) && !isset($annotations['method']['coversNothing'])) {
                $this->addFailure($test, new \ECSPrefix20210803\PHPUnit\Framework\MissingCoversAnnotationException('This test does not have a @covers annotation but is expected to have one'), $time);
                $risky = \true;
            }
        }
        if ($collectCodeCoverage) {
            $append = !$risky && !$incomplete && !$skipped;
            $linesToBeCovered = [];
            $linesToBeUsed = [];
            if ($append && $test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase) {
                try {
                    $linesToBeCovered = \ECSPrefix20210803\PHPUnit\Util\Test::getLinesToBeCovered(\get_class($test), $test->getName(\false));
                    $linesToBeUsed = \ECSPrefix20210803\PHPUnit\Util\Test::getLinesToBeUsed(\get_class($test), $test->getName(\false));
                } catch (\ECSPrefix20210803\PHPUnit\Framework\InvalidCoversTargetException $cce) {
                    $this->addWarning($test, new \ECSPrefix20210803\PHPUnit\Framework\Warning($cce->getMessage()), $time);
                }
            }
            try {
                $this->codeCoverage->stop($append, $linesToBeCovered, $linesToBeUsed);
            } catch (\ECSPrefix20210803\SebastianBergmann\CodeCoverage\UnintentionallyCoveredCodeException $cce) {
                $unintentionallyCoveredCodeError = new \ECSPrefix20210803\PHPUnit\Framework\UnintentionallyCoveredCodeError('This test executed code that is not listed as code to be covered or used:' . \PHP_EOL . $cce->getMessage());
            } catch (\ECSPrefix20210803\SebastianBergmann\CodeCoverage\Exception $cce) {
                $error = \true;
                $e = $e ?? $cce;
            }
        }
        if (isset($errorHandler)) {
            $errorHandler->unregister();
            unset($errorHandler);
        }
        if ($error) {
            $this->addError($test, $e, $time);
        } elseif ($failure) {
            $this->addFailure($test, $e, $time);
        } elseif ($warning) {
            $this->addWarning($test, $e, $time);
        } elseif (isset($unintentionallyCoveredCodeError)) {
            $this->addFailure($test, $unintentionallyCoveredCodeError, $time);
        } elseif ($this->beStrictAboutTestsThatDoNotTestAnything && !$test->doesNotPerformAssertions() && $test->getNumAssertions() === 0) {
            try {
                $reflected = new \ReflectionClass($test);
                // @codeCoverageIgnoreStart
            } catch (\ReflectionException $e) {
                throw new \ECSPrefix20210803\PHPUnit\Framework\Exception($e->getMessage(), (int) $e->getCode(), $e);
            }
            // @codeCoverageIgnoreEnd
            $name = $test->getName(\false);
            if ($name && $reflected->hasMethod($name)) {
                try {
                    $reflected = $reflected->getMethod($name);
                    // @codeCoverageIgnoreStart
                } catch (\ReflectionException $e) {
                    throw new \ECSPrefix20210803\PHPUnit\Framework\Exception($e->getMessage(), (int) $e->getCode(), $e);
                }
                // @codeCoverageIgnoreEnd
            }
            $this->addFailure($test, new \ECSPrefix20210803\PHPUnit\Framework\RiskyTestError(\sprintf("This test did not perform any assertions\n\n%s:%d", $reflected->getFileName(), $reflected->getStartLine())), $time);
        } elseif ($this->beStrictAboutTestsThatDoNotTestAnything && $test->doesNotPerformAssertions() && $test->getNumAssertions() > 0) {
            $this->addFailure($test, new \ECSPrefix20210803\PHPUnit\Framework\RiskyTestError(\sprintf('This test is annotated with "@doesNotPerformAssertions" but performed %d assertions', $test->getNumAssertions())), $time);
        } elseif ($this->beStrictAboutOutputDuringTests && $test->hasOutput()) {
            $this->addFailure($test, new \ECSPrefix20210803\PHPUnit\Framework\OutputError(\sprintf('This test printed output: %s', $test->getActualOutput())), $time);
        } elseif ($this->beStrictAboutTodoAnnotatedTests && $test instanceof \ECSPrefix20210803\PHPUnit\Framework\TestCase) {
            $annotations = \ECSPrefix20210803\PHPUnit\Util\Test::parseTestMethodAnnotations(\get_class($test), $test->getName(\false));
            if (isset($annotations['method']['todo'])) {
                $this->addFailure($test, new \ECSPrefix20210803\PHPUnit\Framework\RiskyTestError('Test method is annotated with @todo'), $time);
            }
        }
        $this->endTest($test, $time);
    }
    /**
     * Gets the number of run tests.
     */
    public function count() : int
    {
        return $this->runTests;
    }
    /**
     * Checks whether the test run should stop.
     */
    public function shouldStop() : bool
    {
        return $this->stop;
    }
    /**
     * Marks that the test run should stop.
     */
    public function stop() : void
    {
        $this->stop = \true;
    }
    /**
     * Returns the code coverage object.
     */
    public function getCodeCoverage() : ?\ECSPrefix20210803\SebastianBergmann\CodeCoverage\CodeCoverage
    {
        return $this->codeCoverage;
    }
    /**
     * Sets the code coverage object.
     */
    public function setCodeCoverage(\ECSPrefix20210803\SebastianBergmann\CodeCoverage\CodeCoverage $codeCoverage) : void
    {
        $this->codeCoverage = $codeCoverage;
    }
    /**
     * Enables or disables the deprecation-to-exception conversion.
     */
    public function convertDeprecationsToExceptions(bool $flag) : void
    {
        $this->convertDeprecationsToExceptions = $flag;
    }
    /**
     * Returns the deprecation-to-exception conversion setting.
     */
    public function getConvertDeprecationsToExceptions() : bool
    {
        return $this->convertDeprecationsToExceptions;
    }
    /**
     * Enables or disables the error-to-exception conversion.
     */
    public function convertErrorsToExceptions(bool $flag) : void
    {
        $this->convertErrorsToExceptions = $flag;
    }
    /**
     * Returns the error-to-exception conversion setting.
     */
    public function getConvertErrorsToExceptions() : bool
    {
        return $this->convertErrorsToExceptions;
    }
    /**
     * Enables or disables the notice-to-exception conversion.
     */
    public function convertNoticesToExceptions(bool $flag) : void
    {
        $this->convertNoticesToExceptions = $flag;
    }
    /**
     * Returns the notice-to-exception conversion setting.
     */
    public function getConvertNoticesToExceptions() : bool
    {
        return $this->convertNoticesToExceptions;
    }
    /**
     * Enables or disables the warning-to-exception conversion.
     */
    public function convertWarningsToExceptions(bool $flag) : void
    {
        $this->convertWarningsToExceptions = $flag;
    }
    /**
     * Returns the warning-to-exception conversion setting.
     */
    public function getConvertWarningsToExceptions() : bool
    {
        return $this->convertWarningsToExceptions;
    }
    /**
     * Enables or disables the stopping when an error occurs.
     */
    public function stopOnError(bool $flag) : void
    {
        $this->stopOnError = $flag;
    }
    /**
     * Enables or disables the stopping when a failure occurs.
     */
    public function stopOnFailure(bool $flag) : void
    {
        $this->stopOnFailure = $flag;
    }
    /**
     * Enables or disables the stopping when a warning occurs.
     */
    public function stopOnWarning(bool $flag) : void
    {
        $this->stopOnWarning = $flag;
    }
    public function beStrictAboutTestsThatDoNotTestAnything(bool $flag) : void
    {
        $this->beStrictAboutTestsThatDoNotTestAnything = $flag;
    }
    public function isStrictAboutTestsThatDoNotTestAnything() : bool
    {
        return $this->beStrictAboutTestsThatDoNotTestAnything;
    }
    public function beStrictAboutOutputDuringTests(bool $flag) : void
    {
        $this->beStrictAboutOutputDuringTests = $flag;
    }
    public function isStrictAboutOutputDuringTests() : bool
    {
        return $this->beStrictAboutOutputDuringTests;
    }
    public function beStrictAboutResourceUsageDuringSmallTests(bool $flag) : void
    {
        $this->beStrictAboutResourceUsageDuringSmallTests = $flag;
    }
    public function isStrictAboutResourceUsageDuringSmallTests() : bool
    {
        return $this->beStrictAboutResourceUsageDuringSmallTests;
    }
    public function enforceTimeLimit(bool $flag) : void
    {
        $this->enforceTimeLimit = $flag;
    }
    public function enforcesTimeLimit() : bool
    {
        return $this->enforceTimeLimit;
    }
    public function beStrictAboutTodoAnnotatedTests(bool $flag) : void
    {
        $this->beStrictAboutTodoAnnotatedTests = $flag;
    }
    public function isStrictAboutTodoAnnotatedTests() : bool
    {
        return $this->beStrictAboutTodoAnnotatedTests;
    }
    public function forceCoversAnnotation() : void
    {
        $this->forceCoversAnnotation = \true;
    }
    public function forcesCoversAnnotation() : bool
    {
        return $this->forceCoversAnnotation;
    }
    /**
     * Enables or disables the stopping for risky tests.
     */
    public function stopOnRisky(bool $flag) : void
    {
        $this->stopOnRisky = $flag;
    }
    /**
     * Enables or disables the stopping for incomplete tests.
     */
    public function stopOnIncomplete(bool $flag) : void
    {
        $this->stopOnIncomplete = $flag;
    }
    /**
     * Enables or disables the stopping for skipped tests.
     */
    public function stopOnSkipped(bool $flag) : void
    {
        $this->stopOnSkipped = $flag;
    }
    /**
     * Enables or disables the stopping for defects: error, failure, warning.
     */
    public function stopOnDefect(bool $flag) : void
    {
        $this->stopOnDefect = $flag;
    }
    /**
     * Returns the time spent running the tests.
     */
    public function time() : float
    {
        return $this->time;
    }
    /**
     * Returns whether the entire test was successful or not.
     */
    public function wasSuccessful() : bool
    {
        return $this->wasSuccessfulIgnoringWarnings() && empty($this->warnings);
    }
    public function wasSuccessfulIgnoringWarnings() : bool
    {
        return empty($this->errors) && empty($this->failures);
    }
    public function wasSuccessfulAndNoTestIsRiskyOrSkippedOrIncomplete() : bool
    {
        return $this->wasSuccessful() && $this->allHarmless() && $this->allCompletelyImplemented() && $this->noneSkipped();
    }
    /**
     * Sets the default timeout for tests.
     */
    public function setDefaultTimeLimit(int $timeout) : void
    {
        $this->defaultTimeLimit = $timeout;
    }
    /**
     * Sets the timeout for small tests.
     */
    public function setTimeoutForSmallTests(int $timeout) : void
    {
        $this->timeoutForSmallTests = $timeout;
    }
    /**
     * Sets the timeout for medium tests.
     */
    public function setTimeoutForMediumTests(int $timeout) : void
    {
        $this->timeoutForMediumTests = $timeout;
    }
    /**
     * Sets the timeout for large tests.
     */
    public function setTimeoutForLargeTests(int $timeout) : void
    {
        $this->timeoutForLargeTests = $timeout;
    }
    /**
     * Returns the set timeout for large tests.
     */
    public function getTimeoutForLargeTests() : int
    {
        return $this->timeoutForLargeTests;
    }
    public function setRegisterMockObjectsFromTestArgumentsRecursively(bool $flag) : void
    {
        $this->registerMockObjectsFromTestArgumentsRecursively = $flag;
    }
    private function recordError(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t) : void
    {
        $this->errors[] = new \ECSPrefix20210803\PHPUnit\Framework\TestFailure($test, $t);
    }
    private function recordNotImplemented(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t) : void
    {
        $this->notImplemented[] = new \ECSPrefix20210803\PHPUnit\Framework\TestFailure($test, $t);
    }
    private function recordRisky(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t) : void
    {
        $this->risky[] = new \ECSPrefix20210803\PHPUnit\Framework\TestFailure($test, $t);
    }
    private function recordSkipped(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t) : void
    {
        $this->skipped[] = new \ECSPrefix20210803\PHPUnit\Framework\TestFailure($test, $t);
    }
    private function recordWarning(\ECSPrefix20210803\PHPUnit\Framework\Test $test, \Throwable $t) : void
    {
        $this->warnings[] = new \ECSPrefix20210803\PHPUnit\Framework\TestFailure($test, $t);
    }
}
