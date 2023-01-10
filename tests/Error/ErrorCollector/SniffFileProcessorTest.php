<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class SniffFileProcessorTest extends AbstractKernelTestCase
{
    private SniffFileProcessor $sniffFileProcessor;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/SniffRunnerSource/easy-coding-standard.php']
        );

        $this->sniffFileProcessor = $this->getService(SniffFileProcessor::class);

        $changedFilesDetector = $this->getService(ChangedFilesDetector::class);
        $changedFilesDetector->clearCache();
    }

    public function test(): void
    {
        $configuration = new Configuration();

        $errorsAndFileDiffs = $this->sniffFileProcessor->processFile(
            __DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc',
            $configuration
        );

        /** @var FileDiff[] $fileDiffs */
        $fileDiffs = $errorsAndFileDiffs['file_diffs'] ?? [];
        $this->assertCount(1, $fileDiffs);

        /** @var CodingStandardError[] $codingStandardErrors */
        $codingStandardErrors = $errorsAndFileDiffs['coding_standard_errors'] ?? [];
        $this->assertCount(0, $codingStandardErrors);
    }
}
