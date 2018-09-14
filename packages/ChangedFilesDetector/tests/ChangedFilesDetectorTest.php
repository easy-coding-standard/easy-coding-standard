<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Tests;

use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;
use function Safe\sprintf;

final class ChangedFilesDetectorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var SplFileInfo
     */
    private $fileInfo;

    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    protected function setUp(): void
    {
        $this->fileInfo = new SplFileInfo(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php',
            'ChangedFilesDetectorSource',
            'ChangedFilesDetectorSource/OneClass.php'
        );

        $this->changedFilesDetector = $this->container->get(ChangedFilesDetector::class);
        $this->changedFilesDetector->changeConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/easy-coding-standard.yml'
        );
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

        $this->changedFilesDetector->changeConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/another-configuration.yml'
        );

        $this->assertFileHasChanged($this->fileInfo);
    }

    private function assertFileHasChanged(SplFileInfo $fileInfo): void
    {
        $this->assertTrue(
            $this->changedFilesDetector->hasFileInfoChanged($fileInfo),
            sprintf('Failed asserting that file "%s" has changed.', $fileInfo->getPathname())
        );
    }

    private function assertFileHasNotChanged(SplFileInfo $fileInfo): void
    {
        $this->assertFalse(
            $this->changedFilesDetector->hasFileInfoChanged($fileInfo),
            sprintf('Failed asserting that file "%s" has not changed.', $fileInfo->getPathname())
        );
    }
}
