<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\ChangedFilesDetector\ChangedFilesDetector;

use SplFileInfo;
use Symplify\EasyCodingStandard\Caching\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class ChangedFilesDetectorTest extends AbstractKernelTestCase
{
    private SplFileInfo $fileInfo;

    private ChangedFilesDetector $changedFilesDetector;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->fileInfo = new SplFileInfo(__DIR__ . '/Source/OneClass.php');

        $this->changedFilesDetector = $this->getService(ChangedFilesDetector::class);
        $this->changedFilesDetector->changeConfigurationFile(__DIR__ . '/Source/easy-coding-standard.php');
    }

    public function testAddFile(): void
    {
        $this->assertFileHasChanged($this->fileInfo);
        $this->assertFileHasChanged($this->fileInfo);
    }

    public function testHasFileChanged(): void
    {
        $this->changedFilesDetector->addFileInfo($this->fileInfo);

        $this->assertFileHasNotChanged($this->fileInfo);
    }

    public function testInvalidateCacheOnConfigurationChange(): void
    {
        $this->changedFilesDetector->addFileInfo($this->fileInfo);
        $this->assertFileHasNotChanged($this->fileInfo);

        $this->changedFilesDetector->changeConfigurationFile(__DIR__ . '/Source/another-configuration.php');

        $this->assertFileHasChanged($this->fileInfo);
    }

    private function assertFileHasChanged(SplFileInfo $fileInfo): void
    {
        $this->assertTrue($this->changedFilesDetector->hasFileInfoChanged($fileInfo));
    }

    private function assertFileHasNotChanged(SplFileInfo $fileInfo): void
    {
        $this->assertFalse($this->changedFilesDetector->hasFileInfoChanged($fileInfo));
    }
}
