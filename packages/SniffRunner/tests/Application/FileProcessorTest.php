<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Application\CurrentFileProvider;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class FileProcessorTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private $initialFileContent;

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @var CurrentFileProvider
     */
    private $currentFileProvider;

    protected function setUp(): void
    {
        static::bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/FileProcessorSource/easy-coding-standard.yml']
        );
        $this->makeConsoleOutputQuite();

        $this->sniffFileProcessor = self::$container->get(SniffFileProcessor::class);
        $this->currentFileProvider = self::$container->get(CurrentFileProvider::class);
    }

    public function test(): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . '/FileProcessorSource/SomeFile.php.inc');
        $this->currentFileProvider->setFileInfo($smartFileInfo);

        $fixedContent = $this->sniffFileProcessor->processFile($smartFileInfo);
        $this->assertNotSame($this->initialFileContent, $fixedContent);
    }

    public function testGetSniffs(): void
    {
        $sniffs = $this->sniffFileProcessor->getCheckers();
        $this->assertCount(1, $sniffs);
    }

    public function testFileProvingNeedOfProperSupportOfChangesets(): void
    {
        static::bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/easy-coding-standard.yml']
        );
        $this->makeConsoleOutputQuite();

        $smartFileInfo = new SmartFileInfo(
            __DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/FileProvingNeedOfProperSupportOfChangesets.php.inc'
        );

        $currentFileProvider = self::$container->get(CurrentFileProvider::class);
        $currentFileProvider->setFileInfo($smartFileInfo);

        $sniffFileProcessor = self::$container->get(SniffFileProcessor::class);
        $this->assertStringEqualsFile(
            __DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/FileProvingNeedOfProperSupportOfChangesets-fixed.php.inc',
            $sniffFileProcessor->processFile($smartFileInfo)
        );
    }

    private function makeConsoleOutputQuite(): void
    {
        $easyCodingStandardStyle = self::$container->get(EasyCodingStandardStyle::class);
        $easyCodingStandardStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }
}
