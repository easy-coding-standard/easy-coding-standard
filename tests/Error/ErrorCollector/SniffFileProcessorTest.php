<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Error\FileDiff;
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
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/SniffRunnerSource/easy-coding-standard.neon'
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

        $this->assertSame(0, $this->errorAndDiffCollector->getErrorCount());
        $this->assertSame(1, $this->errorAndDiffCollector->getFileDiffsCount());

        $fileDiffs = $this->errorAndDiffCollector->getFileDiffs();

        $fileDiff = array_pop($fileDiffs)[0];

        $this->assertInstanceOf(FileDiff::class, $fileDiff);
    }

    private function runFileProcessor(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc', '', '');
        $this->sniffFileProcessor->processFile($fileInfo);
    }
}
