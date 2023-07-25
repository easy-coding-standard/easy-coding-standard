<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Parallel\ValueObject\Bridge;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractTestCase;
use Symplify\EasyCodingStandard\ValueObject\Configuration;

final class FixerFileProcessorTest extends AbstractTestCase
{
    public function test(): void
    {
        $this->createContainerWithConfigs([__DIR__ . '/FixerRunnerSource/phpunit-fixer-config.php']);

        $fixerFileProcessor = $this->make(FixerFileProcessor::class);

        $configuration = new Configuration();

        $errorsAndFileDiffs = $fixerFileProcessor->processFile(
            __DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc',
            $configuration
        );

        $this->assertArrayNotHasKey(Bridge::CODING_STANDARD_ERRORS, $errorsAndFileDiffs);
        $this->assertArrayHasKey(Bridge::FILE_DIFFS, $errorsAndFileDiffs);

        $fileDiffs = $errorsAndFileDiffs[Bridge::FILE_DIFFS];
        $this->assertCount(1, $fileDiffs);
    }
}
