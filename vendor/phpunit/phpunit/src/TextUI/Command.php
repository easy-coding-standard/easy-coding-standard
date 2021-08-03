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
namespace ECSPrefix20210803\PHPUnit\TextUI;

use const PATH_SEPARATOR;
use const PHP_EOL;
use const STDIN;
use function array_keys;
use function assert;
use function class_exists;
use function copy;
use function extension_loaded;
use function fgets;
use function file_get_contents;
use function file_put_contents;
use function get_class;
use function getcwd;
use function ini_get;
use function ini_set;
use function is_callable;
use function is_dir;
use function is_file;
use function is_string;
use function printf;
use function realpath;
use function sort;
use function sprintf;
use function stream_resolve_include_path;
use function strpos;
use function trim;
use function version_compare;
use ECSPrefix20210803\PHPUnit\Framework\TestSuite;
use ECSPrefix20210803\PHPUnit\Runner\Extension\PharLoader;
use ECSPrefix20210803\PHPUnit\Runner\StandardTestSuiteLoader;
use ECSPrefix20210803\PHPUnit\Runner\TestSuiteLoader;
use ECSPrefix20210803\PHPUnit\Runner\Version;
use ECSPrefix20210803\PHPUnit\TextUI\CliArguments\Builder;
use ECSPrefix20210803\PHPUnit\TextUI\CliArguments\Configuration;
use ECSPrefix20210803\PHPUnit\TextUI\CliArguments\Exception as ArgumentsException;
use ECSPrefix20210803\PHPUnit\TextUI\CliArguments\Mapper;
use ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\FilterMapper;
use ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Generator;
use ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Loader;
use ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Migrator;
use ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\PhpHandler;
use ECSPrefix20210803\PHPUnit\Util\FileLoader;
use ECSPrefix20210803\PHPUnit\Util\Filesystem;
use ECSPrefix20210803\PHPUnit\Util\Printer;
use ECSPrefix20210803\PHPUnit\Util\TextTestListRenderer;
use ECSPrefix20210803\PHPUnit\Util\Xml\SchemaDetector;
use ECSPrefix20210803\PHPUnit\Util\XmlTestListRenderer;
use ReflectionClass;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter;
use ECSPrefix20210803\SebastianBergmann\CodeCoverage\StaticAnalysis\CacheWarmer;
use ECSPrefix20210803\SebastianBergmann\Timer\Timer;
use Throwable;
/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 */
class Command
{
    /**
     * @var array<string,mixed>
     */
    protected $arguments = [];
    /**
     * @var array<string,mixed>
     */
    protected $longOptions = [];
    /**
     * @var bool
     */
    private $versionStringPrinted = \false;
    /**
     * @psalm-var list<string>
     */
    private $warnings = [];
    /**
     * @throws Exception
     */
    public static function main(bool $exit = \true) : int
    {
        try {
            return (new static())->run($_SERVER['argv'], $exit);
        } catch (\Throwable $t) {
            throw new \ECSPrefix20210803\PHPUnit\TextUI\RuntimeException($t->getMessage(), (int) $t->getCode(), $t);
        }
    }
    /**
     * @throws Exception
     */
    public function run(array $argv, bool $exit = \true) : int
    {
        $this->handleArguments($argv);
        $runner = $this->createRunner();
        if ($this->arguments['test'] instanceof \ECSPrefix20210803\PHPUnit\Framework\TestSuite) {
            $suite = $this->arguments['test'];
        } else {
            $suite = $runner->getTest($this->arguments['test'], $this->arguments['testSuffixes']);
        }
        if ($this->arguments['listGroups']) {
            return $this->handleListGroups($suite, $exit);
        }
        if ($this->arguments['listSuites']) {
            return $this->handleListSuites($exit);
        }
        if ($this->arguments['listTests']) {
            return $this->handleListTests($suite, $exit);
        }
        if ($this->arguments['listTestsXml']) {
            return $this->handleListTestsXml($suite, $this->arguments['listTestsXml'], $exit);
        }
        unset($this->arguments['test'], $this->arguments['testFile']);
        try {
            $result = $runner->run($suite, $this->arguments, $this->warnings, $exit);
        } catch (\Throwable $t) {
            print $t->getMessage() . \PHP_EOL;
        }
        $return = \ECSPrefix20210803\PHPUnit\TextUI\TestRunner::FAILURE_EXIT;
        if (isset($result) && $result->wasSuccessful()) {
            $return = \ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT;
        } elseif (!isset($result) || $result->errorCount() > 0) {
            $return = \ECSPrefix20210803\PHPUnit\TextUI\TestRunner::EXCEPTION_EXIT;
        }
        if ($exit) {
            exit($return);
        }
        return $return;
    }
    /**
     * Create a TestRunner, override in subclasses.
     */
    protected function createRunner() : \ECSPrefix20210803\PHPUnit\TextUI\TestRunner
    {
        return new \ECSPrefix20210803\PHPUnit\TextUI\TestRunner($this->arguments['loader']);
    }
    /**
     * Handles the command-line arguments.
     *
     * A child class of PHPUnit\TextUI\Command can hook into the argument
     * parsing by adding the switch(es) to the $longOptions array and point to a
     * callback method that handles the switch(es) in the child class like this
     *
     * <code>
     * <?php
     * class MyCommand extends PHPUnit\TextUI\Command
     * {
     *     public function __construct()
     *     {
     *         // my-switch won't accept a value, it's an on/off
     *         $this->longOptions['my-switch'] = 'myHandler';
     *         // my-secondswitch will accept a value - note the equals sign
     *         $this->longOptions['my-secondswitch='] = 'myOtherHandler';
     *     }
     *
     *     // --my-switch  -> myHandler()
     *     protected function myHandler()
     *     {
     *     }
     *
     *     // --my-secondswitch foo -> myOtherHandler('foo')
     *     protected function myOtherHandler ($value)
     *     {
     *     }
     *
     *     // You will also need this - the static keyword in the
     *     // PHPUnit\TextUI\Command will mean that it'll be
     *     // PHPUnit\TextUI\Command that gets instantiated,
     *     // not MyCommand
     *     public static function main($exit = true)
     *     {
     *         $command = new static;
     *
     *         return $command->run($_SERVER['argv'], $exit);
     *     }
     *
     * }
     * </code>
     *
     * @throws Exception
     */
    protected function handleArguments(array $argv) : void
    {
        try {
            $arguments = (new \ECSPrefix20210803\PHPUnit\TextUI\CliArguments\Builder())->fromParameters($argv, \array_keys($this->longOptions));
        } catch (\ECSPrefix20210803\PHPUnit\TextUI\CliArguments\Exception $e) {
            $this->exitWithErrorMessage($e->getMessage());
        }
        \assert(isset($arguments) && $arguments instanceof \ECSPrefix20210803\PHPUnit\TextUI\CliArguments\Configuration);
        if ($arguments->hasGenerateConfiguration() && $arguments->generateConfiguration()) {
            $this->generateConfiguration();
        }
        if ($arguments->hasAtLeastVersion()) {
            if (\version_compare(\ECSPrefix20210803\PHPUnit\Runner\Version::id(), $arguments->atLeastVersion(), '>=')) {
                exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT);
            }
            exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::FAILURE_EXIT);
        }
        if ($arguments->hasVersion() && $arguments->version()) {
            $this->printVersionString();
            exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT);
        }
        if ($arguments->hasCheckVersion() && $arguments->checkVersion()) {
            $this->handleVersionCheck();
        }
        if ($arguments->hasHelp()) {
            $this->showHelp();
            exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT);
        }
        if ($arguments->hasUnrecognizedOrderBy()) {
            $this->exitWithErrorMessage(\sprintf('unrecognized --order-by option: %s', $arguments->unrecognizedOrderBy()));
        }
        if ($arguments->hasIniSettings()) {
            foreach ($arguments->iniSettings() as $name => $value) {
                \ini_set($name, $value);
            }
        }
        if ($arguments->hasIncludePath()) {
            \ini_set('include_path', $arguments->includePath() . \PATH_SEPARATOR . \ini_get('include_path'));
        }
        $this->arguments = (new \ECSPrefix20210803\PHPUnit\TextUI\CliArguments\Mapper())->mapToLegacyArray($arguments);
        $this->handleCustomOptions($arguments->unrecognizedOptions());
        $this->handleCustomTestSuite();
        if (!isset($this->arguments['testSuffixes'])) {
            $this->arguments['testSuffixes'] = ['Test.php', '.phpt'];
        }
        if (!isset($this->arguments['test']) && $arguments->hasArgument()) {
            $this->arguments['test'] = \realpath($arguments->argument());
            if ($this->arguments['test'] === \false) {
                $this->exitWithErrorMessage(\sprintf('Cannot open file "%s".', $arguments->argument()));
            }
        }
        if ($this->arguments['loader'] !== null) {
            $this->arguments['loader'] = $this->handleLoader($this->arguments['loader']);
        }
        if (isset($this->arguments['configuration'])) {
            if (\is_dir($this->arguments['configuration'])) {
                $candidate = $this->configurationFileInDirectory($this->arguments['configuration']);
                if ($candidate !== null) {
                    $this->arguments['configuration'] = $candidate;
                }
            }
        } elseif ($this->arguments['useDefaultConfiguration']) {
            $candidate = $this->configurationFileInDirectory(\getcwd());
            if ($candidate !== null) {
                $this->arguments['configuration'] = $candidate;
            }
        }
        if ($arguments->hasMigrateConfiguration() && $arguments->migrateConfiguration()) {
            if (!isset($this->arguments['configuration'])) {
                print 'No configuration file found to migrate.' . \PHP_EOL;
                exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::EXCEPTION_EXIT);
            }
            $this->migrateConfiguration(\realpath($this->arguments['configuration']));
        }
        if (isset($this->arguments['configuration'])) {
            try {
                $this->arguments['configurationObject'] = (new \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Loader())->load($this->arguments['configuration']);
            } catch (\Throwable $e) {
                print $e->getMessage() . \PHP_EOL;
                exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::FAILURE_EXIT);
            }
            $phpunitConfiguration = $this->arguments['configurationObject']->phpunit();
            (new \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\PhpHandler())->handle($this->arguments['configurationObject']->php());
            if (isset($this->arguments['bootstrap'])) {
                $this->handleBootstrap($this->arguments['bootstrap']);
            } elseif ($phpunitConfiguration->hasBootstrap()) {
                $this->handleBootstrap($phpunitConfiguration->bootstrap());
            }
            if (!isset($this->arguments['stderr'])) {
                $this->arguments['stderr'] = $phpunitConfiguration->stderr();
            }
            if (!isset($this->arguments['noExtensions']) && $phpunitConfiguration->hasExtensionsDirectory() && \extension_loaded('phar')) {
                $result = (new \ECSPrefix20210803\PHPUnit\Runner\Extension\PharLoader())->loadPharExtensionsInDirectory($phpunitConfiguration->extensionsDirectory());
                $this->arguments['loadedExtensions'] = $result['loadedExtensions'];
                $this->arguments['notLoadedExtensions'] = $result['notLoadedExtensions'];
                unset($result);
            }
            if (!isset($this->arguments['columns'])) {
                $this->arguments['columns'] = $phpunitConfiguration->columns();
            }
            if (!isset($this->arguments['printer']) && $phpunitConfiguration->hasPrinterClass()) {
                $file = $phpunitConfiguration->hasPrinterFile() ? $phpunitConfiguration->printerFile() : '';
                $this->arguments['printer'] = $this->handlePrinter($phpunitConfiguration->printerClass(), $file);
            }
            if ($phpunitConfiguration->hasTestSuiteLoaderClass()) {
                $file = $phpunitConfiguration->hasTestSuiteLoaderFile() ? $phpunitConfiguration->testSuiteLoaderFile() : '';
                $this->arguments['loader'] = $this->handleLoader($phpunitConfiguration->testSuiteLoaderClass(), $file);
            }
            if (!isset($this->arguments['testsuite']) && $phpunitConfiguration->hasDefaultTestSuite()) {
                $this->arguments['testsuite'] = $phpunitConfiguration->defaultTestSuite();
            }
            if (!isset($this->arguments['test'])) {
                try {
                    $this->arguments['test'] = (new \ECSPrefix20210803\PHPUnit\TextUI\TestSuiteMapper())->map($this->arguments['configurationObject']->testSuite(), $this->arguments['testsuite'] ?? '');
                } catch (\ECSPrefix20210803\PHPUnit\TextUI\Exception $e) {
                    $this->printVersionString();
                    print $e->getMessage() . \PHP_EOL;
                    exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::EXCEPTION_EXIT);
                }
            }
        } elseif (isset($this->arguments['bootstrap'])) {
            $this->handleBootstrap($this->arguments['bootstrap']);
        }
        if (isset($this->arguments['printer']) && \is_string($this->arguments['printer'])) {
            $this->arguments['printer'] = $this->handlePrinter($this->arguments['printer']);
        }
        if (isset($this->arguments['configurationObject'], $this->arguments['warmCoverageCache'])) {
            $this->handleWarmCoverageCache($this->arguments['configurationObject']);
        }
        if (!isset($this->arguments['test'])) {
            $this->showHelp();
            exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::EXCEPTION_EXIT);
        }
    }
    /**
     * Handles the loading of the PHPUnit\Runner\TestSuiteLoader implementation.
     *
     * @deprecated see https://github.com/sebastianbergmann/phpunit/issues/4039
     */
    protected function handleLoader(string $loaderClass, string $loaderFile = '') : ?\ECSPrefix20210803\PHPUnit\Runner\TestSuiteLoader
    {
        $this->warnings[] = 'Using a custom test suite loader is deprecated';
        if (!\class_exists($loaderClass, \false)) {
            if ($loaderFile == '') {
                $loaderFile = \ECSPrefix20210803\PHPUnit\Util\Filesystem::classNameToFilename($loaderClass);
            }
            $loaderFile = \stream_resolve_include_path($loaderFile);
            if ($loaderFile) {
                /**
                 * @noinspection PhpIncludeInspection
                 * @psalm-suppress UnresolvableInclude
                 */
                require $loaderFile;
            }
        }
        if (\class_exists($loaderClass, \false)) {
            try {
                $class = new \ReflectionClass($loaderClass);
                // @codeCoverageIgnoreStart
            } catch (\ReflectionException $e) {
                throw new \ECSPrefix20210803\PHPUnit\TextUI\ReflectionException($e->getMessage(), (int) $e->getCode(), $e);
            }
            // @codeCoverageIgnoreEnd
            if ($class->implementsInterface(\ECSPrefix20210803\PHPUnit\Runner\TestSuiteLoader::class) && $class->isInstantiable()) {
                $object = $class->newInstance();
                \assert($object instanceof \ECSPrefix20210803\PHPUnit\Runner\TestSuiteLoader);
                return $object;
            }
        }
        if ($loaderClass == \ECSPrefix20210803\PHPUnit\Runner\StandardTestSuiteLoader::class) {
            return null;
        }
        $this->exitWithErrorMessage(\sprintf('Could not use "%s" as loader.', $loaderClass));
        return null;
    }
    /**
     * Handles the loading of the PHPUnit\Util\Printer implementation.
     *
     * @return null|Printer|string
     */
    protected function handlePrinter(string $printerClass, string $printerFile = '')
    {
        if (!\class_exists($printerClass, \false)) {
            if ($printerFile === '') {
                $printerFile = \ECSPrefix20210803\PHPUnit\Util\Filesystem::classNameToFilename($printerClass);
            }
            $printerFile = \stream_resolve_include_path($printerFile);
            if ($printerFile) {
                /**
                 * @noinspection PhpIncludeInspection
                 * @psalm-suppress UnresolvableInclude
                 */
                require $printerFile;
            }
        }
        if (!\class_exists($printerClass)) {
            $this->exitWithErrorMessage(\sprintf('Could not use "%s" as printer: class does not exist', $printerClass));
        }
        try {
            $class = new \ReflectionClass($printerClass);
            // @codeCoverageIgnoreStart
        } catch (\ReflectionException $e) {
            throw new \ECSPrefix20210803\PHPUnit\TextUI\ReflectionException($e->getMessage(), (int) $e->getCode(), $e);
            // @codeCoverageIgnoreEnd
        }
        if (!$class->implementsInterface(\ECSPrefix20210803\PHPUnit\TextUI\ResultPrinter::class)) {
            $this->exitWithErrorMessage(\sprintf('Could not use "%s" as printer: class does not implement %s', $printerClass, \ECSPrefix20210803\PHPUnit\TextUI\ResultPrinter::class));
        }
        if (!$class->isInstantiable()) {
            $this->exitWithErrorMessage(\sprintf('Could not use "%s" as printer: class cannot be instantiated', $printerClass));
        }
        if ($class->isSubclassOf(\ECSPrefix20210803\PHPUnit\TextUI\ResultPrinter::class)) {
            return $printerClass;
        }
        $outputStream = isset($this->arguments['stderr']) ? 'php://stderr' : null;
        return $class->newInstance($outputStream);
    }
    /**
     * Loads a bootstrap file.
     */
    protected function handleBootstrap(string $filename) : void
    {
        try {
            \ECSPrefix20210803\PHPUnit\Util\FileLoader::checkAndLoad($filename);
        } catch (\Throwable $t) {
            if ($t instanceof \ECSPrefix20210803\PHPUnit\Exception) {
                $this->exitWithErrorMessage($t->getMessage());
            }
            $this->exitWithErrorMessage(\sprintf('Error in bootstrap script: %s:%s%s', \get_class($t), \PHP_EOL, $t->getMessage()));
        }
    }
    protected function handleVersionCheck() : void
    {
        $this->printVersionString();
        $latestVersion = \file_get_contents('https://phar.phpunit.de/latest-version-of/phpunit');
        $isOutdated = \version_compare($latestVersion, \ECSPrefix20210803\PHPUnit\Runner\Version::id(), '>');
        if ($isOutdated) {
            \printf('You are not using the latest version of PHPUnit.' . \PHP_EOL . 'The latest version is PHPUnit %s.' . \PHP_EOL, $latestVersion);
        } else {
            print 'You are using the latest version of PHPUnit.' . \PHP_EOL;
        }
        exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT);
    }
    /**
     * Show the help message.
     */
    protected function showHelp() : void
    {
        $this->printVersionString();
        (new \ECSPrefix20210803\PHPUnit\TextUI\Help())->writeToConsole();
    }
    /**
     * Custom callback for test suite discovery.
     */
    protected function handleCustomTestSuite() : void
    {
    }
    private function printVersionString() : void
    {
        if ($this->versionStringPrinted) {
            return;
        }
        print \ECSPrefix20210803\PHPUnit\Runner\Version::getVersionString() . \PHP_EOL . \PHP_EOL;
        $this->versionStringPrinted = \true;
    }
    private function exitWithErrorMessage(string $message) : void
    {
        $this->printVersionString();
        print $message . \PHP_EOL;
        exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::FAILURE_EXIT);
    }
    private function handleListGroups(\ECSPrefix20210803\PHPUnit\Framework\TestSuite $suite, bool $exit) : int
    {
        $this->printVersionString();
        print 'Available test group(s):' . \PHP_EOL;
        $groups = $suite->getGroups();
        \sort($groups);
        foreach ($groups as $group) {
            if (\strpos($group, '__phpunit_') === 0) {
                continue;
            }
            \printf(' - %s' . \PHP_EOL, $group);
        }
        if ($exit) {
            exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT);
        }
        return \ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT;
    }
    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\TextUI\XmlConfiguration\Exception
     */
    private function handleListSuites(bool $exit) : int
    {
        $this->printVersionString();
        print 'Available test suite(s):' . \PHP_EOL;
        foreach ($this->arguments['configurationObject']->testSuite() as $testSuite) {
            \printf(' - %s' . \PHP_EOL, $testSuite->name());
        }
        if ($exit) {
            exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT);
        }
        return \ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT;
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    private function handleListTests(\ECSPrefix20210803\PHPUnit\Framework\TestSuite $suite, bool $exit) : int
    {
        $this->printVersionString();
        $renderer = new \ECSPrefix20210803\PHPUnit\Util\TextTestListRenderer();
        print $renderer->render($suite);
        if ($exit) {
            exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT);
        }
        return \ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT;
    }
    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    private function handleListTestsXml(\ECSPrefix20210803\PHPUnit\Framework\TestSuite $suite, string $target, bool $exit) : int
    {
        $this->printVersionString();
        $renderer = new \ECSPrefix20210803\PHPUnit\Util\XmlTestListRenderer();
        \file_put_contents($target, $renderer->render($suite));
        \printf('Wrote list of tests that would have been run to %s' . \PHP_EOL, $target);
        if ($exit) {
            exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT);
        }
        return \ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT;
    }
    private function generateConfiguration() : void
    {
        $this->printVersionString();
        print 'Generating phpunit.xml in ' . \getcwd() . \PHP_EOL . \PHP_EOL;
        print 'Bootstrap script (relative to path shown above; default: vendor/autoload.php): ';
        $bootstrapScript = \trim(\fgets(\STDIN));
        print 'Tests directory (relative to path shown above; default: tests): ';
        $testsDirectory = \trim(\fgets(\STDIN));
        print 'Source directory (relative to path shown above; default: src): ';
        $src = \trim(\fgets(\STDIN));
        print 'Cache directory (relative to path shown above; default: .phpunit.cache): ';
        $cacheDirectory = \trim(\fgets(\STDIN));
        if ($bootstrapScript === '') {
            $bootstrapScript = 'vendor/autoload.php';
        }
        if ($testsDirectory === '') {
            $testsDirectory = 'tests';
        }
        if ($src === '') {
            $src = 'src';
        }
        if ($cacheDirectory === '') {
            $cacheDirectory = '.phpunit.cache';
        }
        $generator = new \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Generator();
        \file_put_contents('phpunit.xml', $generator->generateDefaultConfiguration(\ECSPrefix20210803\PHPUnit\Runner\Version::series(), $bootstrapScript, $testsDirectory, $src, $cacheDirectory));
        print \PHP_EOL . 'Generated phpunit.xml in ' . \getcwd() . '.' . \PHP_EOL;
        print 'Make sure to exclude the ' . $cacheDirectory . ' directory from version control.' . \PHP_EOL;
        exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT);
    }
    private function migrateConfiguration(string $filename) : void
    {
        $this->printVersionString();
        if (!(new \ECSPrefix20210803\PHPUnit\Util\Xml\SchemaDetector())->detect($filename)->detected()) {
            print $filename . ' does not need to be migrated.' . \PHP_EOL;
            exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::EXCEPTION_EXIT);
        }
        \copy($filename, $filename . '.bak');
        print 'Created backup:         ' . $filename . '.bak' . \PHP_EOL;
        try {
            \file_put_contents($filename, (new \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Migrator())->migrate($filename));
            print 'Migrated configuration: ' . $filename . \PHP_EOL;
        } catch (\Throwable $t) {
            print 'Migration failed: ' . $t->getMessage() . \PHP_EOL;
            exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::EXCEPTION_EXIT);
        }
        exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT);
    }
    private function handleCustomOptions(array $unrecognizedOptions) : void
    {
        foreach ($unrecognizedOptions as $name => $value) {
            if (isset($this->longOptions[$name])) {
                $handler = $this->longOptions[$name];
            }
            $name .= '=';
            if (isset($this->longOptions[$name])) {
                $handler = $this->longOptions[$name];
            }
            if (isset($handler) && \is_callable([$this, $handler])) {
                $this->{$handler}($value);
                unset($handler);
            }
        }
    }
    private function handleWarmCoverageCache(\ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\Configuration $configuration) : void
    {
        $this->printVersionString();
        if (isset($this->arguments['coverageCacheDirectory'])) {
            $cacheDirectory = $this->arguments['coverageCacheDirectory'];
        } elseif ($configuration->codeCoverage()->hasCacheDirectory()) {
            $cacheDirectory = $configuration->codeCoverage()->cacheDirectory()->path();
        } else {
            print 'Cache for static analysis has not been configured' . \PHP_EOL;
            exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::EXCEPTION_EXIT);
        }
        $filter = new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\Filter();
        if ($configuration->codeCoverage()->hasNonEmptyListOfFilesToBeIncludedInCodeCoverageReport()) {
            (new \ECSPrefix20210803\PHPUnit\TextUI\XmlConfiguration\CodeCoverage\FilterMapper())->map($filter, $configuration->codeCoverage());
        } elseif (isset($this->arguments['coverageFilter'])) {
            if (!\is_array($this->arguments['coverageFilter'])) {
                $coverageFilterDirectories = [$this->arguments['coverageFilter']];
            } else {
                $coverageFilterDirectories = $this->arguments['coverageFilter'];
            }
            foreach ($coverageFilterDirectories as $coverageFilterDirectory) {
                $filter->includeDirectory($coverageFilterDirectory);
            }
        } else {
            print 'Filter for code coverage has not been configured' . \PHP_EOL;
            exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::EXCEPTION_EXIT);
        }
        $timer = new \ECSPrefix20210803\SebastianBergmann\Timer\Timer();
        $timer->start();
        print 'Warming cache for static analysis ... ';
        (new \ECSPrefix20210803\SebastianBergmann\CodeCoverage\StaticAnalysis\CacheWarmer())->warmCache($cacheDirectory, !$configuration->codeCoverage()->disableCodeCoverageIgnore(), $configuration->codeCoverage()->ignoreDeprecatedCodeUnits(), $filter);
        print 'done [' . $timer->stop()->asString() . ']' . \PHP_EOL;
        exit(\ECSPrefix20210803\PHPUnit\TextUI\TestRunner::SUCCESS_EXIT);
    }
    private function configurationFileInDirectory(string $directory) : ?string
    {
        $candidates = [$directory . '/phpunit.xml', $directory . '/phpunit.xml.dist'];
        foreach ($candidates as $candidate) {
            if (\is_file($candidate)) {
                return \realpath($candidate);
            }
        }
        return null;
    }
}
