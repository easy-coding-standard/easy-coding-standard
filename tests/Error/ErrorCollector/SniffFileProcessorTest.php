<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Application\CurrentFileProvider;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;

final class SniffFileProcessorTest extends TestCase
{
    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

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
            [__DIR__ . '/SniffRunnerSource/easy-coding-standard.yml']
        );

        $this->errorAndDiffCollector = $container->get(ErrorAndDiffCollector::class);
        $this->sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->currentFileProvider = $container->get(CurrentFileProvider::class);

        $changedFilesDetector = $container->get(ChangedFilesDetector::class);
        $changedFilesDetector->clearCache();
    }

    public function test(): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc');
        $this->currentFileProvider->setFileInfo($smartFileInfo);
        $this->sniffFileProcessor->processFile($smartFileInfo);

        $this->assertSame(2, $this->errorAndDiffCollector->getErrorCount());
        $this->assertSame(0, $this->errorAndDiffCollector->getFileDiffsCount());
    }
}
