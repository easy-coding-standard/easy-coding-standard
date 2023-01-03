<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Testing\PHPUnit;

use ECSPrefix202301\Nette\Utils\FileSystem;
use ECSPrefix202301\Nette\Utils\Strings;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use ECSPrefix202301\Symfony\Component\DependencyInjection\ContainerInterface;
use ECSPrefix202301\Symfony\Component\Finder\Finder;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\Testing\Contract\ConfigAwareInterface;
use Symplify\EasyCodingStandard\Testing\Exception\TestingShouldNotHappenException;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use ECSPrefix202301\Symplify\EasyTesting\StaticFixtureSplitter;
use ECSPrefix202301\Symplify\SmartFileSystem\SmartFileInfo;
use ECSPrefix202301\Webmozart\Assert\Assert;
// needed for scoped version to load unprefixed classes; does not have any effect inside the class
$scoperAutoloadFilepath = __DIR__ . '/../../../vendor/scoper-autoload.php';
if (\file_exists($scoperAutoloadFilepath)) {
    require_once $scoperAutoloadFilepath;
}
abstract class AbstractCheckerTestCase extends TestCase implements ConfigAwareInterface
{
    /**
     * @var string
     */
    private const SPLIT_LINE_REGEX = "#\\-\\-\\-\\-\\-\r?\n#";
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
        $this->fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $this->sniffFileProcessor = $container->get(SniffFileProcessor::class);
    }
    protected function doTestFile(string $filePath) : void
    {
        $this->ensureSomeCheckersAreRegistered();
        $fileContents = FileSystem::read($filePath);
        [$inputContents, $expectedContents] = Strings::split($fileContents, self::SPLIT_LINE_REGEX);
        $inputFilePath = \sys_get_temp_dir() . '/ecs_tests/' . \md5((string) $inputContents) . '.php';
        FileSystem::write($inputFilePath, $inputContents);
        if ($this->fixerFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->fixerFileProcessor->processFileToString($inputFilePath);
            $this->assertEquals($expectedContents, $processedFileContent);
        } elseif ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFileToString($inputFilePath);
        } else {
            throw new TestingShouldNotHappenException();
        }
        $this->assertEquals($expectedContents, $processedFileContent);
    }
    /**
     * @deprecated use doTestFile() instead with \Symplify\EasyCodingStandard\Testing\PHPUnit\StaticFixtureFileFinder::yieldFiles()
     */
    protected function doTestFileInfo(SplFileInfo $fileInfo) : void
    {
        $staticFixtureSplitter = new StaticFixtureSplitter();
        // @deprecated, to be removed in next PR
        $smartFileInfo = new SmartFileInfo($fileInfo->getRealPath());
        $inputFileInfoAndExpectedFileInfo = $staticFixtureSplitter->splitFileInfoToLocalInputAndExpectedFileInfos($smartFileInfo);
        $this->doTestWrongToFixedFile($inputFileInfoAndExpectedFileInfo->getInputFileInfo(), $inputFileInfoAndExpectedFileInfo->getExpectedFileInfoRealPath());
    }
    /**
     * @api
     * File should stay the same and contain 0 errors
     */
    protected function doTestCorrectFileInfo(SplFileInfo $fileInfo) : void
    {
        $this->ensureSomeCheckersAreRegistered();
        if ($this->fixerFileProcessor->getCheckers() !== []) {
            // @todo separate processFile(): array with errors for parallel,
            // and processFileToString() for tests only
            $processedFileContent = $this->fixerFileProcessor->processFileToString($fileInfo);
            $this->assertStringEqualsFile($fileInfo->getRealPath(), $processedFileContent);
        }
        if ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFileToString($fileInfo);
            $this->assertStringEqualsFile($fileInfo->getRealPath(), $processedFileContent);
        }
    }
    /**
     * @api
     */
    protected function doTestFileInfoWithErrorCountOf(SplFileInfo $wrongFileInfo, int $expectedErrorCount) : void
    {
        $this->ensureSomeCheckersAreRegistered();
        $configuration = new Configuration();
        $errorsAndFileDiffs = $this->sniffFileProcessor->processFile($wrongFileInfo, $configuration);
        $errors = $errorsAndFileDiffs[Bridge::CODING_STANDARD_ERRORS] ?? [];
        $message = \sprintf('There should be %d errors in "%s" file, but none found.', $expectedErrorCount, $wrongFileInfo->getRealPath());
        $errorCount = \count($errors);
        $this->assertSame($expectedErrorCount, $errorCount, $message);
    }
    /**
     * @return string[]
     */
    protected static function yieldFiles(string $directory, string $suffix = '*.php.inc') : array
    {
        $finder = Finder::create()->in($directory)->files()->name($suffix);
        $fileInfos = \iterator_to_array($finder);
        $filePaths = \array_keys($fileInfos);
        Assert::allString($filePaths);
        return $filePaths;
    }
    private function doTestWrongToFixedFile(SplFileInfo $wrongFileInfo, string $fixedFile) : void
    {
        $this->ensureSomeCheckersAreRegistered();
        if ($this->fixerFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->fixerFileProcessor->processFileToString($wrongFileInfo);
            $this->assertStringEqualsFile($fixedFile, $processedFileContent);
        } elseif ($this->sniffFileProcessor->getCheckers() !== []) {
            $processedFileContent = $this->sniffFileProcessor->processFileToString($wrongFileInfo);
        } else {
            throw new TestingShouldNotHappenException();
        }
        $this->assertStringEqualsFile($fixedFile, $processedFileContent);
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
        throw new TestingShouldNotHappenException('No fixers nor sniffers were found. Registers them in your config.');
    }
    /**
     * @return string[]
     */
    private function getValidatedConfigs() : array
    {
        $config = $this->provideConfig();
        Assert::fileExists($config);
        return [$config];
    }
    /**
     * @param string[] $configs
     */
    private function bootContainerWithConfigs(array $configs) : ContainerInterface
    {
        Assert::allString($configs);
        Assert::allFile($configs);
        $easyCodingStandardKernel = new EasyCodingStandardKernel();
        return $easyCodingStandardKernel->createFromConfigs($configs);
    }
}
