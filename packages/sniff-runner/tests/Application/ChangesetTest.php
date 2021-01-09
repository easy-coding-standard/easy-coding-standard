<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class ChangesetTest extends AbstractKernelTestCase
{
    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/easy-coding-standard.php']
        );

        $this->sniffFileProcessor = $this->getService(SniffFileProcessor::class);
    }

    public function testFileProvingNeedOfProperSupportOfChangesets(): void
    {
        $smartFileInfo = new SmartFileInfo(
            __DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/FileProvingNeedOfProperSupportOfChangesets.php.inc'
        );

        $changedContent = $this->sniffFileProcessor->processFile($smartFileInfo);
        $this->assertStringEqualsFile(
            __DIR__ . '/FileProcessorSource/ReferenceUsedNamesOnlySniff/FileProvingNeedOfProperSupportOfChangesets-fixed.php.inc',
            $changedContent
        );
    }
}
