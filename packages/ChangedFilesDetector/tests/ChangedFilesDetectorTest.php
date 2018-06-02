<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Tests;

use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class ChangedFilesDetectorTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $phpFile = __DIR__ . '/ChangedFilesDetectorSource/OneClass.php';

    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    protected function setUp(): void
    {
        $this->changedFilesDetector = $this->container->get(ChangedFilesDetector::class);
        $this->changedFilesDetector->changeConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/easy-coding-standard.yml'
        );
    }

    public function testAddFile(): void
    {
        $this->assertFileHasChanged($this->phpFile);
        $this->assertFileHasChanged($this->phpFile);
    }

    public function testHasFileChanged(): void
    {
        $this->changedFilesDetector->addFileInfo($this->phpFile);

        $this->assertFileHasNotChanged($this->phpFile);
    }

    public function testInvalidateCacheOnConfigurationChange(): void
    {
        $this->changedFilesDetector->addFileInfo($this->phpFile);
        $this->assertFileHasNotChanged($this->phpFile);

        $this->changedFilesDetector->changeConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/another-configuration.yml'
        );

        $this->assertFileHasChanged($this->phpFile);
    }

    private function assertFileHasChanged(string $file): void
    {
        $this->assertTrue($this->changedFilesDetector->hasFileInfoChanged($file), sprintf(
            'Failed asserting that file "%s" has changed.',
            $file
        ));
    }

    private function assertFileHasNotChanged(string $file): void
    {
        $this->assertFalse($this->changedFilesDetector->hasFileInfoChanged($file), sprintf(
            'Failed asserting that file "%s" has not changed.',
            $file
        ));
    }
}
