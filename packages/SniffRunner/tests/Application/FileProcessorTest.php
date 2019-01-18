<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\CurrentFileProvider;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class FileProcessorTest extends TestCase
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
        $container = (new ContainerFactory())->createWithConfigs(
            [__DIR__ . '/FileProcessorSource/easy-coding-standard.yml']
        );

        $this->sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->currentFileProvider = $container->get(CurrentFileProvider::class);
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
        $container = (new ContainerFactory())->createWithConfigs(
            [__DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/easy-coding-standard.yml']
        );

        $smartFileInfo = new SmartFileInfo(
            __DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/FileProvingNeedOfProperSupportOfChangesets.php.inc'
        );

        $currentFileProvider = $container->get(CurrentFileProvider::class);
        $currentFileProvider->setFileInfo($smartFileInfo);

        $sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->assertStringEqualsFile(
            __DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/FileProvingNeedOfProperSupportOfChangesets-fixed.php.inc',
            $sniffFileProcessor->processFile($smartFileInfo)
        );
    }
}
