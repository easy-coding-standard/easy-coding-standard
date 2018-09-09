<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

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

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfigs(
            [__DIR__ . '/SniffRunnerSource/easy-coding-standard.yml']
        );

        $this->errorAndDiffCollector = $container->get(ErrorAndDiffCollector::class);
        $this->sniffFileProcessor = $container->get(SniffFileProcessor::class);

        /** @var ChangedFilesDetector $changedFilesDetector */
        $changedFilesDetector = $container->get(ChangedFilesDetector::class);
        $changedFilesDetector->clearCache();
    }

    public function test(): void
    {
        $this->runFileProcessor();

        $this->assertSame(2, $this->errorAndDiffCollector->getErrorCount());
        $this->assertSame(0, $this->errorAndDiffCollector->getFileDiffsCount());
    }

    private function runFileProcessor(): void
    {
        $fileInfo = new SplFileInfo(
            __DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc',
            'ErrorCollectorSource',
            'ErrorCollectorSource/NotPsr2Class.php.inc'
        );

        $this->sniffFileProcessor->processFile($fileInfo);
    }
}
