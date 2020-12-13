<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffResultFactory;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class SniffFileProcessorTest extends AbstractKernelTestCase
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
     * @var ErrorAndDiffResultFactory
     */
    private $errorAndDiffResultFactory;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/SniffRunnerSource/easy-coding-standard.php']
        );

        $this->errorAndDiffCollector = $this->getService(ErrorAndDiffCollector::class);
        $this->errorAndDiffResultFactory = $this->getService(ErrorAndDiffResultFactory::class);
        $this->sniffFileProcessor = $this->getService(SniffFileProcessor::class);

        $changedFilesDetector = $this->getService(ChangedFilesDetector::class);
        $changedFilesDetector->clearCache();
    }

    public function test(): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc');
        $this->sniffFileProcessor->processFile($smartFileInfo);

        $errorAndDiffResult = $this->errorAndDiffResultFactory->create($this->errorAndDiffCollector);

        $this->assertSame(1, $errorAndDiffResult->getErrorCount());
        $this->assertSame(0, $errorAndDiffResult->getFileDiffsCount());
    }
}
