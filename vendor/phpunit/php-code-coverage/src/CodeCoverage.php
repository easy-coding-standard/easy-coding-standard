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
namespace ECSPrefix20210804\SebastianBergmann\CodeCoverage;

use function array_diff;
use function array_diff_key;
use function array_flip;
use function array_keys;
use function array_merge;
use function array_unique;
use function array_values;
use function count;
use function explode;
use function get_class;
use function is_array;
use function is_file;
use function sort;
use ECSPrefix20210804\PHPUnit\Framework\TestCase;
use ECSPrefix20210804\PHPUnit\Runner\PhptTestCase;
use ECSPrefix20210804\PHPUnit\Util\Test;
use ReflectionClass;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\Driver;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\Builder;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\Directory;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\CachingCoveredFileAnalyser;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\CachingUncoveredFileAnalyser;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\CoveredFileAnalyser;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\ParsingCoveredFileAnalyser;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\ParsingUncoveredFileAnalyser;
use ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\UncoveredFileAnalyser;
use ECSPrefix20210804\SebastianBergmann\CodeUnitReverseLookup\Wizard;
/**
 * Provides collection functionality for PHP code coverage information.
 */
final class CodeCoverage
{
    private const UNCOVERED_FILES = 'UNCOVERED_FILES';
    /**
     * @var Driver
     */
    private $driver;
    /**
     * @var Filter
     */
    private $filter;
    /**
     * @var Wizard
     */
    private $wizard;
    /**
     * @var bool
     */
    private $checkForUnintentionallyCoveredCode = \false;
    /**
     * @var bool
     */
    private $includeUncoveredFiles = \true;
    /**
     * @var bool
     */
    private $processUncoveredFiles = \false;
    /**
     * @var bool
     */
    private $ignoreDeprecatedCode = \false;
    /**
     * @var PhptTestCase|string|TestCase
     */
    private $currentId;
    /**
     * Code coverage data.
     *
     * @var ProcessedCodeCoverageData
     */
    private $data;
    /**
     * @var bool
     */
    private $useAnnotationsForIgnoringCode = \true;
    /**
     * Test data.
     *
     * @var array
     */
    private $tests = [];
    /**
     * @psalm-var list<class-string>
     */
    private $parentClassesExcludedFromUnintentionallyCoveredCodeCheck = [];
    /**
     * @var ?CoveredFileAnalyser
     */
    private $coveredFileAnalyser;
    /**
     * @var ?UncoveredFileAnalyser
     */
    private $uncoveredFileAnalyser;
    /**
     * @var ?string
     */
    private $cacheDirectory;
    public function __construct(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\Driver\Driver $driver, \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Filter $filter)
    {
        $this->driver = $driver;
        $this->filter = $filter;
        $this->data = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\ProcessedCodeCoverageData();
        $this->wizard = new \ECSPrefix20210804\SebastianBergmann\CodeUnitReverseLookup\Wizard();
    }
    /**
     * Returns the code coverage information as a graph of node objects.
     */
    public function getReport() : \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\Directory
    {
        return (new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Node\Builder($this->coveredFileAnalyser()))->build($this);
    }
    /**
     * Clears collected code coverage data.
     */
    public function clear() : void
    {
        $this->currentId = null;
        $this->data = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\ProcessedCodeCoverageData();
        $this->tests = [];
    }
    /**
     * Returns the filter object used.
     */
    public function filter() : \ECSPrefix20210804\SebastianBergmann\CodeCoverage\Filter
    {
        return $this->filter;
    }
    /**
     * Returns the collected code coverage data.
     */
    public function getData(bool $raw = \false) : \ECSPrefix20210804\SebastianBergmann\CodeCoverage\ProcessedCodeCoverageData
    {
        if (!$raw) {
            if ($this->processUncoveredFiles) {
                $this->processUncoveredFilesFromFilter();
            } elseif ($this->includeUncoveredFiles) {
                $this->addUncoveredFilesFromFilter();
            }
        }
        return $this->data;
    }
    /**
     * Sets the coverage data.
     */
    public function setData(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\ProcessedCodeCoverageData $data) : void
    {
        $this->data = $data;
    }
    /**
     * Returns the test data.
     */
    public function getTests() : array
    {
        return $this->tests;
    }
    /**
     * Sets the test data.
     */
    public function setTests(array $tests) : void
    {
        $this->tests = $tests;
    }
    /**
     * Start collection of code coverage information.
     *
     * @param PhptTestCase|string|TestCase $id
     */
    public function start($id, bool $clear = \false) : void
    {
        if ($clear) {
            $this->clear();
        }
        $this->currentId = $id;
        $this->driver->start();
    }
    /**
     * Stop collection of code coverage information.
     *
     * @param array|false $linesToBeCovered
     */
    public function stop(bool $append = \true, $linesToBeCovered = [], array $linesToBeUsed = []) : \ECSPrefix20210804\SebastianBergmann\CodeCoverage\RawCodeCoverageData
    {
        if (!\is_array($linesToBeCovered) && $linesToBeCovered !== \false) {
            throw new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\InvalidArgumentException('$linesToBeCovered must be an array or false');
        }
        $data = $this->driver->stop();
        $this->append($data, null, $append, $linesToBeCovered, $linesToBeUsed);
        $this->currentId = null;
        return $data;
    }
    /**
     * Appends code coverage data.
     *
     * @param PhptTestCase|string|TestCase $id
     * @param array|false                  $linesToBeCovered
     *
     * @throws UnintentionallyCoveredCodeException
     * @throws TestIdMissingException
     * @throws ReflectionException
     */
    public function append(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\RawCodeCoverageData $rawData, $id = null, bool $append = \true, $linesToBeCovered = [], array $linesToBeUsed = []) : void
    {
        if ($id === null) {
            $id = $this->currentId;
        }
        if ($id === null) {
            throw new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\TestIdMissingException();
        }
        $this->applyFilter($rawData);
        if ($this->useAnnotationsForIgnoringCode) {
            $this->applyIgnoredLinesFilter($rawData);
        }
        $this->data->initializeUnseenData($rawData);
        if (!$append) {
            return;
        }
        if ($id !== self::UNCOVERED_FILES) {
            $this->applyCoversAnnotationFilter($rawData, $linesToBeCovered, $linesToBeUsed);
            if (empty($rawData->lineCoverage())) {
                return;
            }
            $size = 'unknown';
            $status = -1;
            $fromTestcase = \false;
            if ($id instanceof \ECSPrefix20210804\PHPUnit\Framework\TestCase) {
                $fromTestcase = \true;
                $_size = $id->getSize();
                if ($_size === \ECSPrefix20210804\PHPUnit\Util\Test::SMALL) {
                    $size = 'small';
                } elseif ($_size === \ECSPrefix20210804\PHPUnit\Util\Test::MEDIUM) {
                    $size = 'medium';
                } elseif ($_size === \ECSPrefix20210804\PHPUnit\Util\Test::LARGE) {
                    $size = 'large';
                }
                $status = $id->getStatus();
                $id = \get_class($id) . '::' . $id->getName();
            } elseif ($id instanceof \ECSPrefix20210804\PHPUnit\Runner\PhptTestCase) {
                $fromTestcase = \true;
                $size = 'large';
                $id = $id->getName();
            }
            $this->tests[$id] = ['size' => $size, 'status' => $status, 'fromTestcase' => $fromTestcase];
            $this->data->markCodeAsExecutedByTestCase($id, $rawData);
        }
    }
    /**
     * Merges the data from another instance.
     */
    public function merge(self $that) : void
    {
        $this->filter->includeFiles($that->filter()->files());
        $this->data->merge($that->data);
        $this->tests = \array_merge($this->tests, $that->getTests());
    }
    public function enableCheckForUnintentionallyCoveredCode() : void
    {
        $this->checkForUnintentionallyCoveredCode = \true;
    }
    public function disableCheckForUnintentionallyCoveredCode() : void
    {
        $this->checkForUnintentionallyCoveredCode = \false;
    }
    public function includeUncoveredFiles() : void
    {
        $this->includeUncoveredFiles = \true;
    }
    public function excludeUncoveredFiles() : void
    {
        $this->includeUncoveredFiles = \false;
    }
    public function processUncoveredFiles() : void
    {
        $this->processUncoveredFiles = \true;
    }
    public function doNotProcessUncoveredFiles() : void
    {
        $this->processUncoveredFiles = \false;
    }
    public function enableAnnotationsForIgnoringCode() : void
    {
        $this->useAnnotationsForIgnoringCode = \true;
    }
    public function disableAnnotationsForIgnoringCode() : void
    {
        $this->useAnnotationsForIgnoringCode = \false;
    }
    public function ignoreDeprecatedCode() : void
    {
        $this->ignoreDeprecatedCode = \true;
    }
    public function doNotIgnoreDeprecatedCode() : void
    {
        $this->ignoreDeprecatedCode = \false;
    }
    /**
     * @psalm-assert-if-true !null $this->cacheDirectory
     */
    public function cachesStaticAnalysis() : bool
    {
        return $this->cacheDirectory !== null;
    }
    public function cacheStaticAnalysis(string $directory) : void
    {
        $this->cacheDirectory = $directory;
    }
    public function doNotCacheStaticAnalysis() : void
    {
        $this->cacheDirectory = null;
    }
    /**
     * @throws StaticAnalysisCacheNotConfiguredException
     */
    public function cacheDirectory() : string
    {
        if (!$this->cachesStaticAnalysis()) {
            throw new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysisCacheNotConfiguredException('The static analysis cache is not configured');
        }
        return $this->cacheDirectory;
    }
    /**
     * @psalm-param class-string $className
     */
    public function excludeSubclassesOfThisClassFromUnintentionallyCoveredCodeCheck(string $className) : void
    {
        $this->parentClassesExcludedFromUnintentionallyCoveredCodeCheck[] = $className;
    }
    public function enableBranchAndPathCoverage() : void
    {
        $this->driver->enableBranchAndPathCoverage();
    }
    public function disableBranchAndPathCoverage() : void
    {
        $this->driver->disableBranchAndPathCoverage();
    }
    public function collectsBranchAndPathCoverage() : bool
    {
        return $this->driver->collectsBranchAndPathCoverage();
    }
    public function detectsDeadCode() : bool
    {
        return $this->driver->detectsDeadCode();
    }
    /**
     * Applies the @covers annotation filtering.
     *
     * @param array|false $linesToBeCovered
     *
     * @throws UnintentionallyCoveredCodeException
     * @throws ReflectionException
     */
    private function applyCoversAnnotationFilter(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\RawCodeCoverageData $rawData, $linesToBeCovered, array $linesToBeUsed) : void
    {
        if ($linesToBeCovered === \false) {
            $rawData->clear();
            return;
        }
        if (empty($linesToBeCovered)) {
            return;
        }
        if ($this->checkForUnintentionallyCoveredCode && (!$this->currentId instanceof \ECSPrefix20210804\PHPUnit\Framework\TestCase || !$this->currentId->isMedium() && !$this->currentId->isLarge())) {
            $this->performUnintentionallyCoveredCodeCheck($rawData, $linesToBeCovered, $linesToBeUsed);
        }
        $rawLineData = $rawData->lineCoverage();
        $filesWithNoCoverage = \array_diff_key($rawLineData, $linesToBeCovered);
        foreach (\array_keys($filesWithNoCoverage) as $fileWithNoCoverage) {
            $rawData->removeCoverageDataForFile($fileWithNoCoverage);
        }
        if (\is_array($linesToBeCovered)) {
            foreach ($linesToBeCovered as $fileToBeCovered => $includedLines) {
                $rawData->keepCoverageDataOnlyForLines($fileToBeCovered, $includedLines);
            }
        }
    }
    private function applyFilter(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\RawCodeCoverageData $data) : void
    {
        if ($this->filter->isEmpty()) {
            return;
        }
        foreach (\array_keys($data->lineCoverage()) as $filename) {
            if ($this->filter->isExcluded($filename)) {
                $data->removeCoverageDataForFile($filename);
            }
        }
    }
    private function applyIgnoredLinesFilter(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\RawCodeCoverageData $data) : void
    {
        foreach (\array_keys($data->lineCoverage()) as $filename) {
            if (!$this->filter->isFile($filename)) {
                continue;
            }
            $data->removeCoverageDataForLines($filename, $this->coveredFileAnalyser()->ignoredLinesFor($filename));
        }
    }
    /**
     * @throws UnintentionallyCoveredCodeException
     */
    private function addUncoveredFilesFromFilter() : void
    {
        $uncoveredFiles = \array_diff($this->filter->files(), $this->data->coveredFiles());
        foreach ($uncoveredFiles as $uncoveredFile) {
            if (\is_file($uncoveredFile)) {
                $this->append(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\RawCodeCoverageData::fromUncoveredFile($uncoveredFile, $this->uncoveredFileAnalyser()), self::UNCOVERED_FILES);
            }
        }
    }
    /**
     * @throws UnintentionallyCoveredCodeException
     */
    private function processUncoveredFilesFromFilter() : void
    {
        $uncoveredFiles = \array_diff($this->filter->files(), $this->data->coveredFiles());
        $this->driver->start();
        foreach ($uncoveredFiles as $uncoveredFile) {
            if (\is_file($uncoveredFile)) {
                include_once $uncoveredFile;
            }
        }
        $this->append($this->driver->stop(), self::UNCOVERED_FILES);
    }
    /**
     * @throws UnintentionallyCoveredCodeException
     * @throws ReflectionException
     */
    private function performUnintentionallyCoveredCodeCheck(\ECSPrefix20210804\SebastianBergmann\CodeCoverage\RawCodeCoverageData $data, array $linesToBeCovered, array $linesToBeUsed) : void
    {
        $allowedLines = $this->getAllowedLines($linesToBeCovered, $linesToBeUsed);
        $unintentionallyCoveredUnits = [];
        foreach ($data->lineCoverage() as $file => $_data) {
            foreach ($_data as $line => $flag) {
                if ($flag === 1 && !isset($allowedLines[$file][$line])) {
                    $unintentionallyCoveredUnits[] = $this->wizard->lookup($file, $line);
                }
            }
        }
        $unintentionallyCoveredUnits = $this->processUnintentionallyCoveredUnits($unintentionallyCoveredUnits);
        if (!empty($unintentionallyCoveredUnits)) {
            throw new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\UnintentionallyCoveredCodeException($unintentionallyCoveredUnits);
        }
    }
    private function getAllowedLines(array $linesToBeCovered, array $linesToBeUsed) : array
    {
        $allowedLines = [];
        foreach (\array_keys($linesToBeCovered) as $file) {
            if (!isset($allowedLines[$file])) {
                $allowedLines[$file] = [];
            }
            $allowedLines[$file] = \array_merge($allowedLines[$file], $linesToBeCovered[$file]);
        }
        foreach (\array_keys($linesToBeUsed) as $file) {
            if (!isset($allowedLines[$file])) {
                $allowedLines[$file] = [];
            }
            $allowedLines[$file] = \array_merge($allowedLines[$file], $linesToBeUsed[$file]);
        }
        foreach (\array_keys($allowedLines) as $file) {
            $allowedLines[$file] = \array_flip(\array_unique($allowedLines[$file]));
        }
        return $allowedLines;
    }
    /**
     * @throws ReflectionException
     */
    private function processUnintentionallyCoveredUnits(array $unintentionallyCoveredUnits) : array
    {
        $unintentionallyCoveredUnits = \array_unique($unintentionallyCoveredUnits);
        \sort($unintentionallyCoveredUnits);
        foreach (\array_keys($unintentionallyCoveredUnits) as $k => $v) {
            $unit = \explode('::', $unintentionallyCoveredUnits[$k]);
            if (\count($unit) !== 2) {
                continue;
            }
            try {
                $class = new \ReflectionClass($unit[0]);
                foreach ($this->parentClassesExcludedFromUnintentionallyCoveredCodeCheck as $parentClass) {
                    if ($class->isSubclassOf($parentClass)) {
                        unset($unintentionallyCoveredUnits[$k]);
                        break;
                    }
                }
            } catch (\ReflectionException $e) {
                throw new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\ReflectionException($e->getMessage(), (int) $e->getCode(), $e);
            }
        }
        return \array_values($unintentionallyCoveredUnits);
    }
    private function coveredFileAnalyser() : \ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\CoveredFileAnalyser
    {
        if ($this->coveredFileAnalyser !== null) {
            return $this->coveredFileAnalyser;
        }
        $this->coveredFileAnalyser = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\ParsingCoveredFileAnalyser($this->useAnnotationsForIgnoringCode, $this->ignoreDeprecatedCode);
        if ($this->cachesStaticAnalysis()) {
            $this->coveredFileAnalyser = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\CachingCoveredFileAnalyser($this->cacheDirectory, $this->coveredFileAnalyser);
        }
        return $this->coveredFileAnalyser;
    }
    private function uncoveredFileAnalyser() : \ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\UncoveredFileAnalyser
    {
        if ($this->uncoveredFileAnalyser !== null) {
            return $this->uncoveredFileAnalyser;
        }
        $this->uncoveredFileAnalyser = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\ParsingUncoveredFileAnalyser();
        if ($this->cachesStaticAnalysis()) {
            $this->uncoveredFileAnalyser = new \ECSPrefix20210804\SebastianBergmann\CodeCoverage\StaticAnalysis\CachingUncoveredFileAnalyser($this->cacheDirectory, $this->uncoveredFileAnalyser);
        }
        return $this->uncoveredFileAnalyser;
    }
}
