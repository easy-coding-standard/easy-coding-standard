<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Testing\PHPUnit;

use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\Testing\Contract\ConfigAwareInterface;
use Symplify\EasyCodingStandard\Testing\Exception\ShouldNotHappenException;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\FileSystemGuard;
use Symplify\SmartFileSystem\SmartFileInfo;

abstract class AbstractCheckerTestCase extends AbstractKernelTestCase implements ConfigAwareInterface
{
    /**
     * @var string[]
     */
    const POSSIBLE_CODE_SNIFFER_AUTOLOAD_PATHS = [
        __DIR__ . '/../../../../../vendor/squizlabs/php_codesniffer/autoload.php',
        __DIR__ . '/../../../../vendor/squizlabs/php_codesniffer/autoload.php',
    ];

    /**
     * @var \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor
     */
    private $fixerFileProcessor;

    /**
     * @var \Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @return void
     */
    protected function setUp()
    {
        // autoload php code sniffer before Kernel boot
        $this->autoloadCodeSniffer();

        $configs = $this->getValidatedConfigs();
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, $configs);

        $this->fixerFileProcessor = $this->getService(FixerFileProcessor::class);
        $this->sniffFileProcessor = $this->getService(SniffFileProcessor::class);
    }

    /**
     * @param \Symplify\SmartFileSystem\SmartFileInfo $fileInfo
     * @return void
     */
    protected function doTestFileInfo($fileInfo)
    {
        $staticFixtureSplitter = new StaticFixtureSplitter();

        $inputFileInfoAndExpectedFileInfo = $staticFixtureSplitter->splitFileInfoToLocalInputAndExpectedFileInfos(
            $fileInfo
        );

        $this->doTestWrongToFixedFile(
            $inputFileInfoAndExpectedFileInfo->getInputFileInfo(),
            $inputFileInfoAndExpectedFileInfo->getExpectedFileInfoRealPath(),
            $fileInfo
        );
    }

    /**
     * File should stay the same and contain 0 errors
     * @param \Symplify\SmartFileSystem\SmartFileInfo $fileInfo
     * @return void
     */
    protected function doTestCorrectFileInfo($fileInfo)
    {
        $this->ensureSomeCheckersAreRegistered();

        if ($this->fixerFileProcessor->getCheckers() !== []) {
            // @todo separate processFile(): array with errors for parallel,
            // and processFileToString() for tests only
            $processedFileContent = $this->fixerFileProcessor->processFileToString($fileInfo);
            $this->assertStringEqualsWithFileLocation($fileInfo->getRealPath(), $processedFileContent, $fileInfo);
        }

        if ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFileToString($fileInfo);

            $this->assertStringEqualsWithFileLocation($fileInfo->getRealPath(), $processedFileContent, $fileInfo);
        }
    }

    /**
     * @param \Symplify\SmartFileSystem\SmartFileInfo $wrongFileInfo
     * @param int $expectedErrorCount
     * @return void
     */
    protected function doTestFileInfoWithErrorCountOf($wrongFileInfo, $expectedErrorCount)
    {
        $this->ensureSomeCheckersAreRegistered();

        $configuration = new Configuration();
        $errorsAndFileDiffs = $this->sniffFileProcessor->processFile($wrongFileInfo, $configuration);

        $errors = $errorsAndFileDiffs[Bridge::CODING_STANDARD_ERRORS] ?? [];

        $message = sprintf(
            'There should be %d errors in "%s" file, but none found.',
            $expectedErrorCount,
            $wrongFileInfo->getRealPath()
        );

        $errorCount = count($errors);
        $this->assertSame($expectedErrorCount, $errorCount, $message);
    }

    /**
     * @return void
     */
    private function doTestWrongToFixedFile(
        SmartFileInfo $wrongFileInfo,
        string $fixedFile,
        SmartFileInfo $fixtureFileInfo
    ) {
        $this->ensureSomeCheckersAreRegistered();

        if ($this->fixerFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->fixerFileProcessor->processFileToString($wrongFileInfo);
            $this->assertStringEqualsWithFileLocation($fixedFile, $processedFileContent, $fixtureFileInfo);
        } elseif ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFileToString($wrongFileInfo);
        } else {
            throw new ShouldNotHappenException();
        }

        $this->assertStringEqualsWithFileLocation($fixedFile, $processedFileContent, $fixtureFileInfo);
    }

    /**
     * @return void
     */
    private function autoloadCodeSniffer()
    {
        foreach (self::POSSIBLE_CODE_SNIFFER_AUTOLOAD_PATHS as $possibleCodeSnifferAutoloadPath) {
            if (! file_exists($possibleCodeSnifferAutoloadPath)) {
                continue;
            }

            require_once $possibleCodeSnifferAutoloadPath;
            return;
        }
    }

    /**
     * @return void
     */
    private function ensureSomeCheckersAreRegistered()
    {
        $totalCheckersLoaded = count($this->sniffFileProcessor->getCheckers())
            + count($this->fixerFileProcessor->getCheckers());

        if ($totalCheckersLoaded > 0) {
            return;
        }

        throw new ShouldNotHappenException('No checkers were found. Registers them in your config.');
    }

    /**
     * @return void
     */
    private function assertStringEqualsWithFileLocation(
        string $file,
        string $processedFileContent,
        SmartFileInfo $fixtureFileInfo
    ) {
        $relativeFilePathFromCwd = $fixtureFileInfo->getRelativeFilePathFromCwd();
        $this->assertStringEqualsFile($file, $processedFileContent, $relativeFilePathFromCwd);
    }

    /**
     * @return string[]
     */
    private function getValidatedConfigs(): array
    {
        $config = $this->provideConfig();
        $fileSystemGuard = new FileSystemGuard();
        $fileSystemGuard->ensureFileExists($config, static::class);

        return [$config];
    }
}
