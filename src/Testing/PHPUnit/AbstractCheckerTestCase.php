<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Testing\PHPUnit;

use PHPUnit\Framework\TestCase;
use ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\Testing\Contract\ConfigAwareInterface;
use Symplify\EasyCodingStandard\Testing\Exception\ShouldNotHappenException;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use ECSPrefix20220220\Symplify\EasyTesting\StaticFixtureSplitter;
use ECSPrefix20220220\Symplify\SmartFileSystem\FileSystemGuard;
use ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix20220220\Webmozart\Assert\Assert;
// needed for scoped version to load unprefixed classes; does not have any effect inside the class
$scoperAutoloadFilePath = __DIR__ . '/../../../vendor/scoper-autoload.php';
if (\file_exists($scoperAutoloadFilePath)) {
    require_once $scoperAutoloadFilePath;
}
abstract class AbstractCheckerTestCase extends \PHPUnit\Framework\TestCase implements \Symplify\EasyCodingStandard\Testing\Contract\ConfigAwareInterface
{
    /**
     * @var string[]
     */
    private const POSSIBLE_CODE_SNIFFER_AUTOLOAD_PATHS = [__DIR__ . '/../../../../../vendor/squizlabs/php_codesniffer/autoload.php', __DIR__ . '/../../../../vendor/squizlabs/php_codesniffer/autoload.php'];
    /**
     * @var \Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor
     */
    private $fixerFileProcessor;
    /**
     * @var \Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor
     */
    private $sniffFileProcessor;
    protected function setUp() : void
    {
        // autoload php code sniffer before Kernel boot
        $this->autoloadCodeSniffer();
        $configs = $this->getValidatedConfigs();
        $container = $this->bootContainerWithConfigs($configs);
        $this->fixerFileProcessor = $container->get(\Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor::class);
        $this->sniffFileProcessor = $container->get(\Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor::class);
    }
    protected function doTestFileInfo(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $fileInfo) : void
    {
        $staticFixtureSplitter = new \ECSPrefix20220220\Symplify\EasyTesting\StaticFixtureSplitter();
        $inputFileInfoAndExpectedFileInfo = $staticFixtureSplitter->splitFileInfoToLocalInputAndExpectedFileInfos($fileInfo);
        $this->doTestWrongToFixedFile($inputFileInfoAndExpectedFileInfo->getInputFileInfo(), $inputFileInfoAndExpectedFileInfo->getExpectedFileInfoRealPath(), $fileInfo);
    }
    /**
     * File should stay the same and contain 0 errors
     */
    protected function doTestCorrectFileInfo(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $fileInfo) : void
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
    protected function doTestFileInfoWithErrorCountOf(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $wrongFileInfo, int $expectedErrorCount) : void
    {
        $this->ensureSomeCheckersAreRegistered();
        $configuration = new \Symplify\EasyCodingStandard\ValueObject\Configuration();
        $errorsAndFileDiffs = $this->sniffFileProcessor->processFile($wrongFileInfo, $configuration);
        $errors = $errorsAndFileDiffs[\Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge::CODING_STANDARD_ERRORS] ?? [];
        $message = \sprintf('There should be %d errors in "%s" file, but none found.', $expectedErrorCount, $wrongFileInfo->getRealPath());
        $errorCount = \count($errors);
        $this->assertSame($expectedErrorCount, $errorCount, $message);
    }
    private function doTestWrongToFixedFile(\ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $wrongFileInfo, string $fixedFile, \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo) : void
    {
        $this->ensureSomeCheckersAreRegistered();
        if ($this->fixerFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->fixerFileProcessor->processFileToString($wrongFileInfo);
            $this->assertStringEqualsWithFileLocation($fixedFile, $processedFileContent, $fixtureFileInfo);
        } elseif ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFileToString($wrongFileInfo);
        } else {
            throw new \Symplify\EasyCodingStandard\Testing\Exception\ShouldNotHappenException();
        }
        $this->assertStringEqualsWithFileLocation($fixedFile, $processedFileContent, $fixtureFileInfo);
    }
    private function autoloadCodeSniffer() : void
    {
        foreach (self::POSSIBLE_CODE_SNIFFER_AUTOLOAD_PATHS as $possibleCodeSnifferAutoloadPath) {
            if (!\file_exists($possibleCodeSnifferAutoloadPath)) {
                continue;
            }
            require_once $possibleCodeSnifferAutoloadPath;
            return;
        }
    }
    private function ensureSomeCheckersAreRegistered() : void
    {
        $totalCheckersLoaded = \count($this->sniffFileProcessor->getCheckers()) + \count($this->fixerFileProcessor->getCheckers());
        if ($totalCheckersLoaded > 0) {
            return;
        }
        throw new \Symplify\EasyCodingStandard\Testing\Exception\ShouldNotHappenException('No checkers were found. Registers them in your config.');
    }
    private function assertStringEqualsWithFileLocation(string $file, string $processedFileContent, \ECSPrefix20220220\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo) : void
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
        $fileSystemGuard = new \ECSPrefix20220220\Symplify\SmartFileSystem\FileSystemGuard();
        $fileSystemGuard->ensureFileExists($config, static::class);
        return [$config];
    }
    /**
     * @param string[] $configs
     */
    private function bootContainerWithConfigs(array $configs) : \ECSPrefix20220220\Symfony\Component\DependencyInjection\ContainerInterface
    {
        \ECSPrefix20220220\Webmozart\Assert\Assert::allString($configs);
        \ECSPrefix20220220\Webmozart\Assert\Assert::allFile($configs);
        $easyCodingStandardKernel = new \Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel();
        return $easyCodingStandardKernel->createFromConfigs($configs);
    }
}
