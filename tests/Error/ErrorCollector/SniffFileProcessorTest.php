<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\Error\CodingStandardError;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractTestCase;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\FileDiff;

final class SniffFileProcessorTest extends AbstractTestCase
{
    public function test(): void
    {
        $this->createContainerWithConfigs([__DIR__ . '/SniffRunnerSource/easy-coding-standard.php']);

        $sniffFileProcessor = $this->make(SniffFileProcessor::class);

        $changedFilesDetector = $this->make(ChangedFilesDetector::class);
        $changedFilesDetector->clearCache();

        $errorsAndFileDiffs = $sniffFileProcessor->processFile(
            __DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc',
            new Configuration(),
        );

        /** @var FileDiff[] $fileDiffs */
        $fileDiffs = $errorsAndFileDiffs['file_diffs'] ?? [];
        $this->assertCount(1, $fileDiffs);

        /** @var CodingStandardError[] $codingStandardErrors */
        $codingStandardErrors = $errorsAndFileDiffs['coding_standard_errors'] ?? [];
        $this->assertCount(0, $codingStandardErrors);
    }
}
