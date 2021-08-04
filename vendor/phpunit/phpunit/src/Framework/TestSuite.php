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
namespace ECSPrefix20210804\PHPUnit\Framework;

use const PHP_EOL;
use function array_diff;
use function array_keys;
use function array_map;
use function array_merge;
use function array_unique;
use function basename;
use function call_user_func;
use function class_exists;
use function count;
use function dirname;
use function get_declared_classes;
use function implode;
use function is_bool;
use function is_callable;
use function is_file;
use function is_object;
use function is_string;
use function method_exists;
use function preg_match;
use function preg_quote;
use function sprintf;
use function strpos;
use function substr;
use Iterator;
use IteratorAggregate;
use ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner;
use ECSPrefix20210804\PHPUnit\Runner\Filter\Factory;
use ECSPrefix20210804\PHPUnit\Runner\PhptTestCase;
use ECSPrefix20210804\PHPUnit\Util\FileLoader;
use ECSPrefix20210804\PHPUnit\Util\Test as TestUtil;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Throwable;
/**
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
class TestSuite implements \IteratorAggregate, \ECSPrefix20210804\PHPUnit\Framework\Reorderable, \ECSPrefix20210804\PHPUnit\Framework\SelfDescribing, \ECSPrefix20210804\PHPUnit\Framework\Test
{
    /**
     * Enable or disable the backup and restoration of the $GLOBALS array.
     *
     * @var bool
     */
    protected $backupGlobals;
    /**
     * Enable or disable the backup and restoration of static attributes.
     *
     * @var bool
     */
    protected $backupStaticAttributes;
    /**
     * @var bool
     */
    protected $runTestInSeparateProcess = \false;
    /**
     * The name of the test suite.
     *
     * @var string
     */
    protected $name = '';
    /**
     * The test groups of the test suite.
     *
     * @psalm-var array<string,list<Test>>
     */
    protected $groups = [];
    /**
     * The tests in the test suite.
     *
     * @var Test[]
     */
    protected $tests = [];
    /**
     * The number of tests in the test suite.
     *
     * @var int
     */
    protected $numTests = -1;
    /**
     * @var bool
     */
    protected $testCase = \false;
    /**
     * @var string[]
     */
    protected $foundClasses = [];
    /**
     * @var null|list<ExecutionOrderDependency>
     */
    protected $providedTests;
    /**
     * @var null|list<ExecutionOrderDependency>
     */
    protected $requiredTests;
    /**
     * @var bool
     */
    private $beStrictAboutChangesToGlobalState;
    /**
     * @var Factory
     */
    private $iteratorFilter;
    /**
     * @var string[]
     */
    private $declaredClasses;
    /**
     * @psalm-var array<int,string>
     */
    private $warnings = [];
    /**
     * Constructs a new TestSuite.
     *
     *   - PHPUnit\Framework\TestSuite() constructs an empty TestSuite.
     *
     *   - PHPUnit\Framework\TestSuite(ReflectionClass) constructs a
     *     TestSuite from the given class.
     *
     *   - PHPUnit\Framework\TestSuite(ReflectionClass, String)
     *     constructs a TestSuite from the given class with the given
     *     name.
     *
     *   - PHPUnit\Framework\TestSuite(String) either constructs a
     *     TestSuite from the given class (if the passed string is the
     *     name of an existing class) or constructs an empty TestSuite
     *     with the given name.
     *
     * @param ReflectionClass|string $theClass
     *
     * @throws Exception
     */
    public function __construct($theClass = '', string $name = '')
    {
        if (!\is_string($theClass) && !$theClass instanceof \ReflectionClass) {
            throw \ECSPrefix20210804\PHPUnit\Framework\InvalidArgumentException::create(1, 'ReflectionClass object or string');
        }
        $this->declaredClasses = \get_declared_classes();
        if (!$theClass instanceof \ReflectionClass) {
            if (\class_exists($theClass, \true)) {
                if ($name === '') {
                    $name = $theClass;
                }
                try {
                    $theClass = new \ReflectionClass($theClass);
                } catch (\ReflectionException $e) {
                    throw new \ECSPrefix20210804\PHPUnit\Framework\Exception($e->getMessage(), (int) $e->getCode(), $e);
                }
                // @codeCoverageIgnoreEnd
            } else {
                $this->setName($theClass);
                return;
            }
        }
        if (!$theClass->isSubclassOf(\ECSPrefix20210804\PHPUnit\Framework\TestCase::class)) {
            $this->setName((string) $theClass);
            return;
        }
        if ($name !== '') {
            $this->setName($name);
        } else {
            $this->setName($theClass->getName());
        }
        $constructor = $theClass->getConstructor();
        if ($constructor !== null && !$constructor->isPublic()) {
            $this->addTest(new \ECSPrefix20210804\PHPUnit\Framework\WarningTestCase(\sprintf('Class "%s" has no public constructor.', $theClass->getName())));
            return;
        }
        foreach ($theClass->getMethods() as $method) {
            if ($method->getDeclaringClass()->getName() === \ECSPrefix20210804\PHPUnit\Framework\Assert::class) {
                continue;
            }
            if ($method->getDeclaringClass()->getName() === \ECSPrefix20210804\PHPUnit\Framework\TestCase::class) {
                continue;
            }
            if (!\ECSPrefix20210804\PHPUnit\Util\Test::isTestMethod($method)) {
                continue;
            }
            $this->addTestMethod($theClass, $method);
        }
        if (empty($this->tests)) {
            $this->addTest(new \ECSPrefix20210804\PHPUnit\Framework\WarningTestCase(\sprintf('No tests found in class "%s".', $theClass->getName())));
        }
        $this->testCase = \true;
    }
    /**
     * Returns a string representation of the test suite.
     */
    public function toString() : string
    {
        return $this->getName();
    }
    /**
     * Adds a test to the suite.
     *
     * @param array $groups
     */
    public function addTest(\ECSPrefix20210804\PHPUnit\Framework\Test $test, $groups = []) : void
    {
        try {
            $class = new \ReflectionClass($test);
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new \ECSPrefix20210804\PHPUnit\Framework\Exception($e->getMessage(), (int) $e->getCode(), $e);
        }
        // @codeCoverageIgnoreEnd
        if (!$class->isAbstract()) {
            $this->tests[] = $test;
            $this->clearCaches();
            if ($test instanceof self && empty($groups)) {
                $groups = $test->getGroups();
            }
            if ($this->containsOnlyVirtualGroups($groups)) {
                $groups[] = 'default';
            }
            foreach ($groups as $group) {
                if (!isset($this->groups[$group])) {
                    $this->groups[$group] = [$test];
                } else {
                    $this->groups[$group][] = $test;
                }
            }
            if ($test instanceof \ECSPrefix20210804\PHPUnit\Framework\TestCase) {
                $test->setGroups($groups);
            }
        }
    }
    /**
     * Adds the tests from the given class to the suite.
     *
     * @psalm-param object|class-string $testClass
     *
     * @throws Exception
     */
    public function addTestSuite($testClass) : void
    {
        if (!(\is_object($testClass) || \is_string($testClass) && \class_exists($testClass))) {
            throw \ECSPrefix20210804\PHPUnit\Framework\InvalidArgumentException::create(1, 'class name or object');
        }
        if (!\is_object($testClass)) {
            try {
                $testClass = new \ReflectionClass($testClass);
                // @codeCoverageIgnoreStart
            } catch (\ReflectionException $e) {
                throw new \ECSPrefix20210804\PHPUnit\Framework\Exception($e->getMessage(), (int) $e->getCode(), $e);
            }
            // @codeCoverageIgnoreEnd
        }
        if ($testClass instanceof self) {
            $this->addTest($testClass);
        } elseif ($testClass instanceof \ReflectionClass) {
            $suiteMethod = \false;
            if (!$testClass->isAbstract() && $testClass->hasMethod(\ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::SUITE_METHODNAME)) {
                try {
                    $method = $testClass->getMethod(\ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::SUITE_METHODNAME);
                    // @codeCoverageIgnoreStart
                } catch (\ReflectionException $e) {
                    throw new \ECSPrefix20210804\PHPUnit\Framework\Exception($e->getMessage(), (int) $e->getCode(), $e);
                }
                // @codeCoverageIgnoreEnd
                if ($method->isStatic()) {
                    $this->addTest($method->invoke(null, $testClass->getName()));
                    $suiteMethod = \true;
                }
            }
            if (!$suiteMethod && !$testClass->isAbstract() && $testClass->isSubclassOf(\ECSPrefix20210804\PHPUnit\Framework\TestCase::class)) {
                $this->addTest(new self($testClass));
            }
        } else {
            throw new \ECSPrefix20210804\PHPUnit\Framework\Exception();
        }
    }
    public function addWarning(string $warning) : void
    {
        $this->warnings[] = $warning;
    }
    /**
     * Wraps both <code>addTest()</code> and <code>addTestSuite</code>
     * as well as the separate import statements for the user's convenience.
     *
     * If the named file cannot be read or there are no new tests that can be
     * added, a <code>PHPUnit\Framework\WarningTestCase</code> will be created instead,
     * leaving the current test run untouched.
     *
     * @throws Exception
     */
    public function addTestFile(string $filename) : void
    {
        if (\is_file($filename) && \substr($filename, -5) === '.phpt') {
            $this->addTest(new \ECSPrefix20210804\PHPUnit\Runner\PhptTestCase($filename));
            $this->declaredClasses = \get_declared_classes();
            return;
        }
        $numTests = \count($this->tests);
        // The given file may contain further stub classes in addition to the
        // test class itself. Figure out the actual test class.
        $filename = \ECSPrefix20210804\PHPUnit\Util\FileLoader::checkAndLoad($filename);
        $newClasses = \array_diff(\get_declared_classes(), $this->declaredClasses);
        // The diff is empty in case a parent class (with test methods) is added
        // AFTER a child class that inherited from it. To account for that case,
        // accumulate all discovered classes, so the parent class may be found in
        // a later invocation.
        if (!empty($newClasses)) {
            // On the assumption that test classes are defined first in files,
            // process discovered classes in approximate LIFO order, so as to
            // avoid unnecessary reflection.
            $this->foundClasses = \array_merge($newClasses, $this->foundClasses);
            $this->declaredClasses = \get_declared_classes();
        }
        // The test class's name must match the filename, either in full, or as
        // a PEAR/PSR-0 prefixed short name ('NameSpace_ShortName'), or as a
        // PSR-1 local short name ('NameSpace\ShortName'). The comparison must be
        // anchored to prevent false-positive matches (e.g., 'OtherShortName').
        $shortName = \basename($filename, '.php');
        $shortNameRegEx = '/(?:^|_|\\\\)' . \preg_quote($shortName, '/') . '$/';
        foreach ($this->foundClasses as $i => $className) {
            if (\preg_match($shortNameRegEx, $className)) {
                try {
                    $class = new \ReflectionClass($className);
                    // @codeCoverageIgnoreStart
                } catch (\ReflectionException $e) {
                    throw new \ECSPrefix20210804\PHPUnit\Framework\Exception($e->getMessage(), (int) $e->getCode(), $e);
                }
                // @codeCoverageIgnoreEnd
                if ($class->getFileName() == $filename) {
                    $newClasses = [$className];
                    unset($this->foundClasses[$i]);
                    break;
                }
            }
        }
        foreach ($newClasses as $className) {
            try {
                $class = new \ReflectionClass($className);
                // @codeCoverageIgnoreStart
            } catch (\ReflectionException $e) {
                throw new \ECSPrefix20210804\PHPUnit\Framework\Exception($e->getMessage(), (int) $e->getCode(), $e);
            }
            // @codeCoverageIgnoreEnd
            if (\dirname($class->getFileName()) === __DIR__) {
                continue;
            }
            if (!$class->isAbstract()) {
                if ($class->hasMethod(\ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::SUITE_METHODNAME)) {
                    try {
                        $method = $class->getMethod(\ECSPrefix20210804\PHPUnit\Runner\BaseTestRunner::SUITE_METHODNAME);
                        // @codeCoverageIgnoreStart
                    } catch (\ReflectionException $e) {
                        throw new \ECSPrefix20210804\PHPUnit\Framework\Exception($e->getMessage(), (int) $e->getCode(), $e);
                    }
                    // @codeCoverageIgnoreEnd
                    if ($method->isStatic()) {
                        $this->addTest($method->invoke(null, $className));
                    }
                } elseif ($class->implementsInterface(\ECSPrefix20210804\PHPUnit\Framework\Test::class)) {
                    $expectedClassName = $shortName;
                    if (($pos = \strpos($expectedClassName, '.')) !== \false) {
                        $expectedClassName = \substr($expectedClassName, 0, $pos);
                    }
                    if ($class->getShortName() !== $expectedClassName) {
                        $this->addWarning(\sprintf("Test case class not matching filename is deprecated\n               in %s\n               Class name was '%s', expected '%s'", $filename, $class->getShortName(), $expectedClassName));
                    }
                    $this->addTestSuite($class);
                }
            }
        }
        if (\count($this->tests) > ++$numTests) {
            $this->addWarning(\sprintf("Multiple test case classes per file is deprecated\n               in %s", $filename));
        }
        $this->numTests = -1;
    }
    /**
     * Wrapper for addTestFile() that adds multiple test files.
     *
     * @throws Exception
     */
    public function addTestFiles(iterable $fileNames) : void
    {
        foreach ($fileNames as $filename) {
            $this->addTestFile((string) $filename);
        }
    }
    /**
     * Counts the number of test cases that will be run by this test.
     *
     * @todo refactor usage of numTests in DefaultResultPrinter
     */
    public function count() : int
    {
        $this->numTests = 0;
        foreach ($this as $test) {
            $this->numTests += \count($test);
        }
        return $this->numTests;
    }
    /**
     * Returns the name of the suite.
     */
    public function getName() : string
    {
        return $this->name;
    }
    /**
     * Returns the test groups of the suite.
     *
     * @psalm-return list<string>
     */
    public function getGroups() : array
    {
        return \array_map(static function ($key) : string {
            return (string) $key;
        }, \array_keys($this->groups));
    }
    public function getGroupDetails() : array
    {
        return $this->groups;
    }
    /**
     * Set tests groups of the test case.
     */
    public function setGroupDetails(array $groups) : void
    {
        $this->groups = $groups;
    }
    /**
     * Runs the tests and collects their result in a TestResult.
     *
     * @throws \PHPUnit\Framework\CodeCoverageException
     * @throws \SebastianBergmann\CodeCoverage\InvalidArgumentException
     * @throws \SebastianBergmann\CodeCoverage\UnintentionallyCoveredCodeException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Warning
     */
    public function run(\ECSPrefix20210804\PHPUnit\Framework\TestResult $result = null) : \ECSPrefix20210804\PHPUnit\Framework\TestResult
    {
        if ($result === null) {
            $result = $this->createResult();
        }
        if (\count($this) === 0) {
            return $result;
        }
        /** @psalm-var class-string $className */
        $className = $this->name;
        $hookMethods = \ECSPrefix20210804\PHPUnit\Util\Test::getHookMethods($className);
        $result->startTestSuite($this);
        $test = null;
        if ($this->testCase && \class_exists($this->name, \false)) {
            try {
                foreach ($hookMethods['beforeClass'] as $beforeClassMethod) {
                    if (\method_exists($this->name, $beforeClassMethod)) {
                        if ($missingRequirements = \ECSPrefix20210804\PHPUnit\Util\Test::getMissingRequirements($this->name, $beforeClassMethod)) {
                            $this->markTestSuiteSkipped(\implode(\PHP_EOL, $missingRequirements));
                        }
                        \call_user_func([$this->name, $beforeClassMethod]);
                    }
                }
            } catch (\ECSPrefix20210804\PHPUnit\Framework\SkippedTestSuiteError $error) {
                foreach ($this->tests() as $test) {
                    $result->startTest($test);
                    $result->addFailure($test, $error, 0);
                    $result->endTest($test, 0);
                }
                $result->endTestSuite($this);
                return $result;
            } catch (\Throwable $t) {
                $errorAdded = \false;
                foreach ($this->tests() as $test) {
                    if ($result->shouldStop()) {
                        break;
                    }
                    $result->startTest($test);
                    if (!$errorAdded) {
                        $result->addError($test, $t, 0);
                        $errorAdded = \true;
                    } else {
                        $result->addFailure($test, new \ECSPrefix20210804\PHPUnit\Framework\SkippedTestError('Test skipped because of an error in hook method'), 0);
                    }
                    $result->endTest($test, 0);
                }
                $result->endTestSuite($this);
                return $result;
            }
        }
        foreach ($this as $test) {
            if ($result->shouldStop()) {
                break;
            }
            if ($test instanceof \ECSPrefix20210804\PHPUnit\Framework\TestCase || $test instanceof self) {
                $test->setBeStrictAboutChangesToGlobalState($this->beStrictAboutChangesToGlobalState);
                $test->setBackupGlobals($this->backupGlobals);
                $test->setBackupStaticAttributes($this->backupStaticAttributes);
                $test->setRunTestInSeparateProcess($this->runTestInSeparateProcess);
            }
            $test->run($result);
        }
        if ($this->testCase && \class_exists($this->name, \false)) {
            foreach ($hookMethods['afterClass'] as $afterClassMethod) {
                if (\method_exists($this->name, $afterClassMethod)) {
                    try {
                        \call_user_func([$this->name, $afterClassMethod]);
                    } catch (\Throwable $t) {
                        $message = "Exception in {$this->name}::{$afterClassMethod}" . \PHP_EOL . $t->getMessage();
                        $error = new \ECSPrefix20210804\PHPUnit\Framework\SyntheticError($message, 0, $t->getFile(), $t->getLine(), $t->getTrace());
                        $placeholderTest = clone $test;
                        $placeholderTest->setName($afterClassMethod);
                        $result->startTest($placeholderTest);
                        $result->addFailure($placeholderTest, $error, 0);
                        $result->endTest($placeholderTest, 0);
                    }
                }
            }
        }
        $result->endTestSuite($this);
        return $result;
    }
    public function setRunTestInSeparateProcess(bool $runTestInSeparateProcess) : void
    {
        $this->runTestInSeparateProcess = $runTestInSeparateProcess;
    }
    public function setName(string $name) : void
    {
        $this->name = $name;
    }
    /**
     * Returns the tests as an enumeration.
     *
     * @return Test[]
     */
    public function tests() : array
    {
        return $this->tests;
    }
    /**
     * Set tests of the test suite.
     *
     * @param Test[] $tests
     */
    public function setTests(array $tests) : void
    {
        $this->tests = $tests;
    }
    /**
     * Mark the test suite as skipped.
     *
     * @param string $message
     *
     * @throws SkippedTestSuiteError
     *
     * @psalm-return never-return
     */
    public function markTestSuiteSkipped($message = '') : void
    {
        throw new \ECSPrefix20210804\PHPUnit\Framework\SkippedTestSuiteError($message);
    }
    /**
     * @param bool $beStrictAboutChangesToGlobalState
     */
    public function setBeStrictAboutChangesToGlobalState($beStrictAboutChangesToGlobalState) : void
    {
        if (null === $this->beStrictAboutChangesToGlobalState && \is_bool($beStrictAboutChangesToGlobalState)) {
            $this->beStrictAboutChangesToGlobalState = $beStrictAboutChangesToGlobalState;
        }
    }
    /**
     * @param bool $backupGlobals
     */
    public function setBackupGlobals($backupGlobals) : void
    {
        if (null === $this->backupGlobals && \is_bool($backupGlobals)) {
            $this->backupGlobals = $backupGlobals;
        }
    }
    /**
     * @param bool $backupStaticAttributes
     */
    public function setBackupStaticAttributes($backupStaticAttributes) : void
    {
        if (null === $this->backupStaticAttributes && \is_bool($backupStaticAttributes)) {
            $this->backupStaticAttributes = $backupStaticAttributes;
        }
    }
    /**
     * Returns an iterator for this test suite.
     */
    public function getIterator() : \Iterator
    {
        $iterator = new \ECSPrefix20210804\PHPUnit\Framework\TestSuiteIterator($this);
        if ($this->iteratorFilter !== null) {
            $iterator = $this->iteratorFilter->factory($iterator, $this);
        }
        return $iterator;
    }
    public function injectFilter(\ECSPrefix20210804\PHPUnit\Runner\Filter\Factory $filter) : void
    {
        $this->iteratorFilter = $filter;
        foreach ($this as $test) {
            if ($test instanceof self) {
                $test->injectFilter($filter);
            }
        }
    }
    /**
     * @psalm-return array<int,string>
     */
    public function warnings() : array
    {
        return \array_unique($this->warnings);
    }
    /**
     * @return list<ExecutionOrderDependency>
     */
    public function provides() : array
    {
        if ($this->providedTests === null) {
            $this->providedTests = [];
            if (\is_callable($this->sortId(), \true)) {
                $this->providedTests[] = new \ECSPrefix20210804\PHPUnit\Framework\ExecutionOrderDependency($this->sortId());
            }
            foreach ($this->tests as $test) {
                if (!$test instanceof \ECSPrefix20210804\PHPUnit\Framework\Reorderable) {
                    // @codeCoverageIgnoreStart
                    continue;
                    // @codeCoverageIgnoreEnd
                }
                $this->providedTests = \ECSPrefix20210804\PHPUnit\Framework\ExecutionOrderDependency::mergeUnique($this->providedTests, $test->provides());
            }
        }
        return $this->providedTests;
    }
    /**
     * @return list<ExecutionOrderDependency>
     */
    public function requires() : array
    {
        if ($this->requiredTests === null) {
            $this->requiredTests = [];
            foreach ($this->tests as $test) {
                if (!$test instanceof \ECSPrefix20210804\PHPUnit\Framework\Reorderable) {
                    // @codeCoverageIgnoreStart
                    continue;
                    // @codeCoverageIgnoreEnd
                }
                $this->requiredTests = \ECSPrefix20210804\PHPUnit\Framework\ExecutionOrderDependency::mergeUnique(\ECSPrefix20210804\PHPUnit\Framework\ExecutionOrderDependency::filterInvalid($this->requiredTests), $test->requires());
            }
            $this->requiredTests = \ECSPrefix20210804\PHPUnit\Framework\ExecutionOrderDependency::diff($this->requiredTests, $this->provides());
        }
        return $this->requiredTests;
    }
    public function sortId() : string
    {
        return $this->getName() . '::class';
    }
    /**
     * Creates a default TestResult object.
     */
    protected function createResult() : \ECSPrefix20210804\PHPUnit\Framework\TestResult
    {
        return new \ECSPrefix20210804\PHPUnit\Framework\TestResult();
    }
    /**
     * @throws Exception
     */
    protected function addTestMethod(\ReflectionClass $class, \ReflectionMethod $method) : void
    {
        $methodName = $method->getName();
        $test = (new \ECSPrefix20210804\PHPUnit\Framework\TestBuilder())->build($class, $methodName);
        if ($test instanceof \ECSPrefix20210804\PHPUnit\Framework\TestCase || $test instanceof \ECSPrefix20210804\PHPUnit\Framework\DataProviderTestSuite) {
            $test->setDependencies(\ECSPrefix20210804\PHPUnit\Util\Test::getDependencies($class->getName(), $methodName));
        }
        $this->addTest($test, \ECSPrefix20210804\PHPUnit\Util\Test::getGroups($class->getName(), $methodName));
    }
    private function clearCaches() : void
    {
        $this->numTests = -1;
        $this->providedTests = null;
        $this->requiredTests = null;
    }
    private function containsOnlyVirtualGroups(array $groups) : bool
    {
        foreach ($groups as $group) {
            if (\strpos($group, '__phpunit_') !== 0) {
                return \false;
            }
        }
        return \true;
    }
}
