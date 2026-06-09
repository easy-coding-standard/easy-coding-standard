<?php

declare (strict_types=1);
namespace ECSPrefix202606\TomasVotruba\ClassLeak\Commands;

use Closure;
use ECSPrefix202606\Entropy\Console\Contract\CommandInterface;
use ECSPrefix202606\Entropy\Console\Output\OutputPrinter;
use ECSPrefix202606\TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter;
use ECSPrefix202606\TomasVotruba\ClassLeak\Finder\ClassNamesFinder;
use ECSPrefix202606\TomasVotruba\ClassLeak\Finder\PhpFilesFinder;
use ECSPrefix202606\TomasVotruba\ClassLeak\Reporting\UnusedClassesResultFactory;
use ECSPrefix202606\TomasVotruba\ClassLeak\Reporting\UnusedClassReporter;
use ECSPrefix202606\TomasVotruba\ClassLeak\UseImportsResolver;
final class CheckCommand implements CommandInterface
{
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\Finder\ClassNamesFinder
     */
    private $classNamesFinder;
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\UseImportsResolver
     */
    private $useImportsResolver;
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\Filtering\PossiblyUnusedClassesFilter
     */
    private $possiblyUnusedClassesFilter;
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\Reporting\UnusedClassReporter
     */
    private $unusedClassReporter;
    /**
     * @readonly
     * @var \Entropy\Console\Output\OutputPrinter
     */
    private $outputPrinter;
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\Finder\PhpFilesFinder
     */
    private $phpFilesFinder;
    /**
     * @readonly
     * @var \TomasVotruba\ClassLeak\Reporting\UnusedClassesResultFactory
     */
    private $unusedClassesResultFactory;
    public function __construct(ClassNamesFinder $classNamesFinder, UseImportsResolver $useImportsResolver, PossiblyUnusedClassesFilter $possiblyUnusedClassesFilter, UnusedClassReporter $unusedClassReporter, OutputPrinter $outputPrinter, PhpFilesFinder $phpFilesFinder, UnusedClassesResultFactory $unusedClassesResultFactory)
    {
        $this->classNamesFinder = $classNamesFinder;
        $this->useImportsResolver = $useImportsResolver;
        $this->possiblyUnusedClassesFilter = $possiblyUnusedClassesFilter;
        $this->unusedClassReporter = $unusedClassReporter;
        $this->outputPrinter = $outputPrinter;
        $this->phpFilesFinder = $phpFilesFinder;
        $this->unusedClassesResultFactory = $unusedClassesResultFactory;
    }
    public function getName(): string
    {
        return 'check';
    }
    public function getDescription(): string
    {
        return 'Check classes that are not used in any config and in the code';
    }
    /**
     * @api called by entropy console via reflection
     *
     * @option $skipType
     * @option $skipSuffix
     * @option $skipPath
     * @option $skipAttribute
     * @option $fileExtension
     *
     * @param string[] $paths Files and directories to analyze
     * @param string[] $skipType Class types that should be skipped
     * @param string[] $skipSuffix Class suffix that should be skipped
     * @param string[] $skipPath Paths to skip (real path or just directory name)
     * @param string[] $skipAttribute Class attribute that should be skipped
     * @param bool $includeEntities Include Doctrine ORM and ODM entities (skipped by default)
     * @param string[] $fileExtension File extensions to check
     * @param bool $json Output as JSON
     * @param bool $ansi Kept for backward compatibility, colored output is always on
     */
    public function run(array $paths, array $skipType = [], array $skipSuffix = [], array $skipPath = [], array $skipAttribute = [], bool $includeEntities = \false, array $fileExtension = ['php'], bool $json = \false, bool $ansi = \false): int
    {
        // we have to look for usage in every path
        $allFilePaths = $this->phpFilesFinder->findPhpFiles($paths, $fileExtension, []);
        // but we only want to check the files that are not in the skipped paths
        $phpFilePaths = $this->phpFilesFinder->findPhpFiles($paths, $fileExtension, $skipPath);
        $progressCallback = null;
        if (!$json) {
            $this->outputPrinter->title('1. Finding used classes');
            $progressCallback = $this->createProgressCallback(count($allFilePaths));
        }
        $usedNames = $this->resolveUsedClassNames($allFilePaths, $progressCallback);
        $this->outputPrinter->newline(2);
        $progressCallback = null;
        if (!$json) {
            $this->outputPrinter->title('2. Extracting existing files with classes');
            $progressCallback = $this->createProgressCallback(count($phpFilePaths));
        }
        $existingFilesWithClasses = $this->classNamesFinder->resolveClassNamesToCheck($phpFilePaths, $progressCallback);
        $this->outputPrinter->newline(2);
        $possiblyUnusedFilesWithClasses = $this->possiblyUnusedClassesFilter->filter($existingFilesWithClasses, $usedNames, $skipType, $skipSuffix, $skipAttribute, $includeEntities);
        $unusedClassesResult = $this->unusedClassesResultFactory->create($possiblyUnusedFilesWithClasses);
        $this->outputPrinter->newline();
        return $this->unusedClassReporter->reportResult($unusedClassesResult, $json);
    }
    /**
     * @param string[] $phpFilePaths
     * @return string[]
     */
    private function resolveUsedClassNames(array $phpFilePaths, ?Closure $progressCallback): array
    {
        $usedNames = [];
        foreach ($phpFilePaths as $phpFilePath) {
            $currentUsedNames = $this->useImportsResolver->resolve($phpFilePath);
            $usedNames = array_merge($usedNames, $currentUsedNames);
            ($nullsafeVariable1 = $progressCallback) ? $nullsafeVariable1->__invoke() : null;
        }
        $usedNames = array_unique($usedNames);
        sort($usedNames);
        return $usedNames;
    }
    private function createProgressCallback(int $max): ?Closure
    {
        // avoid printing to stdout during unit tests
        if (defined('PHPUNIT_COMPOSER_INSTALL')) {
            return null;
        }
        $current = 0;
        return static function () use (&$current, $max): void {
            ++$current;
            fwrite(\STDOUT, sprintf("\r%d/%d", $current, $max));
        };
    }
}
