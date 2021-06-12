<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Testing\PHPUnit;

use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffResultFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\Testing\Contract\ConfigAwareInterface;
use Symplify\EasyCodingStandard\Testing\Exception\ShouldNotHappenException;
use ECSPrefix20210612\Symplify\EasyTesting\StaticFixtureSplitter;
use ECSPrefix20210612\Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use ECSPrefix20210612\Symplify\SmartFileSystem\FileSystemGuard;
use ECSPrefix20210612\Symplify\SmartFileSystem\SmartFileInfo;
abstract class AbstractCheckerTestCase extends \ECSPrefix20210612\Symplify\PackageBuilder\Testing\AbstractKernelTestCase implements \Symplify\EasyCodingStandard\Testing\Contract\ConfigAwareInterface
{
    /**
     * @var string[]
     */
    const POSSIBLE_CODE_SNIFFER_AUTOLOAD_PATHS = [__DIR__ . '/../../../../../vendor/squizlabs/php_codesniffer/autoload.php', __DIR__ . '/../../../../vendor/squizlabs/php_codesniffer/autoload.php'];
    /**
     * @var \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor
     */
    private $fixerFileProcessor;
    /**
     * @var \Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor
     */
    private $sniffFileProcessor;
    /**
     * @var \Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;
    /**
     * @var \Symplify\EasyCodingStandard\Error\ErrorAndDiffResultFactory
     */
    private $errorAndDiffResultFactory;
    /**
     * @return void
     */
    protected function setUp()
    {
        // autoload php code sniffer before Kernel boot
        $this->autoloadCodeSniffer();
        $configs = $this->getValidatedConfigs();
        $this->bootKernelWithConfigs(\Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel::class, $configs);
        $this->fixerFileProcessor = $this->getService(\Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor::class);
        $this->sniffFileProcessor = $this->getService(\Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor::class);
        $this->errorAndDiffCollector = $this->getService(\Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector::class);
        $this->errorAndDiffResultFactory = $this->getService(\Symplify\EasyCodingStandard\Error\ErrorAndDiffResultFactory::class);
        // reset error count from previous possibly container cached run
        $this->errorAndDiffCollector->resetCounters();
    }
    /**
     * @return void
     */
    protected function doTestFileInfo(\ECSPrefix20210612\Symplify\SmartFileSystem\SmartFileInfo $fileInfo)
    {
        $staticFixtureSplitter = new \ECSPrefix20210612\Symplify\EasyTesting\StaticFixtureSplitter();
        $inputFileInfoAndExpectedFileInfo = $staticFixtureSplitter->splitFileInfoToLocalInputAndExpectedFileInfos($fileInfo);
        $this->doTestWrongToFixedFile($inputFileInfoAndExpectedFileInfo->getInputFileInfo(), $inputFileInfoAndExpectedFileInfo->getExpectedFileInfoRealPath(), $fileInfo);
    }
    /**
     * File should stay the same and contain 0 errors
     * @return void
     */
    protected function doTestCorrectFileInfo(\ECSPrefix20210612\Symplify\SmartFileSystem\SmartFileInfo $fileInfo)
    {
        $this->errorAndDiffCollector->resetCounters();
        $this->ensureSomeCheckersAreRegistered();
        if ($this->fixerFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->fixerFileProcessor->processFile($fileInfo);
            $this->assertStringEqualsWithFileLocation($fileInfo->getRealPath(), $processedFileContent, $fileInfo);
        }
        if ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFile($fileInfo);
            $errorAndDiffResult = $this->errorAndDiffResultFactory->create();
            $failedAssertMessage = \sprintf('There should be no error in "%s" file, but %d errors found.', $errorAndDiffResult->getErrorCount(), $fileInfo->getRealPath());
            $this->assertSame(0, $errorAndDiffResult->getErrorCount(), $failedAssertMessage);
            $this->assertStringEqualsWithFileLocation($fileInfo->getRealPath(), $processedFileContent, $fileInfo);
        }
    }
    /**
     * @return void
     */
    protected function doTestFileInfoWithErrorCountOf(\ECSPrefix20210612\Symplify\SmartFileSystem\SmartFileInfo $wrongFileInfo, int $expectedErrorCount)
    {
        $this->ensureSomeCheckersAreRegistered();
        $this->errorAndDiffCollector->resetCounters();
        $this->sniffFileProcessor->processFile($wrongFileInfo);
        $message = \sprintf('There should be %d error(s) in "%s" file, but none found.', $expectedErrorCount, $wrongFileInfo->getRealPath());
        $errorAndDiffResult = $this->errorAndDiffResultFactory->create();
        $this->assertSame($expectedErrorCount, $errorAndDiffResult->getErrorCount(), $message);
    }
    /**
     * @return void
     */
    private function doTestWrongToFixedFile(\ECSPrefix20210612\Symplify\SmartFileSystem\SmartFileInfo $wrongFileInfo, string $fixedFile, \ECSPrefix20210612\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo)
    {
        $processedFileContent = null;
        $this->ensureSomeCheckersAreRegistered();
        if ($this->fixerFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->fixerFileProcessor->processFile($wrongFileInfo);
            $this->assertStringEqualsWithFileLocation($fixedFile, $processedFileContent, $fixtureFileInfo);
        }
        if ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFile($wrongFileInfo);
        }
        if ($processedFileContent === null) {
            throw new \Symplify\EasyCodingStandard\Testing\Exception\ShouldNotHappenException();
        }
        $this->assertStringEqualsWithFileLocation($fixedFile, $processedFileContent, $fixtureFileInfo);
    }
    /**
     * @return void
     */
    private function autoloadCodeSniffer()
    {
        foreach (self::POSSIBLE_CODE_SNIFFER_AUTOLOAD_PATHS as $possibleCodeSnifferAutoloadPath) {
            if (!\file_exists($possibleCodeSnifferAutoloadPath)) {
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
        $totalCheckersLoaded = \count($this->sniffFileProcessor->getCheckers()) + \count($this->fixerFileProcessor->getCheckers());
        if ($totalCheckersLoaded > 0) {
            return;
        }
        throw new \Symplify\EasyCodingStandard\Testing\Exception\ShouldNotHappenException('No checkers were found. Registers them in your config.');
    }
    /**
     * @return void
     */
    private function assertStringEqualsWithFileLocation(string $file, string $processedFileContent, \ECSPrefix20210612\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo)
    {
        $relativeFilePathFromCwd = $fixtureFileInfo->getRelativeFilePathFromCwd();
        $this->assertStringEqualsFile($file, $processedFileContent, $relativeFilePathFromCwd);
    }
    /**
     * @return string[]
     */
    private function getValidatedConfigs() : array
    {
        $config = $this->provideConfig();
        $fileSystemGuard = new \ECSPrefix20210612\Symplify\SmartFileSystem\FileSystemGuard();
        $fileSystemGuard->ensureFileExists($config, static::class);
        return [$config];
    }
}
