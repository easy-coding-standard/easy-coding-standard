<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Tests;

use Nette\Utils\FileSystem;
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
        FileSystem::createDir($this->getCacheDirectory());

        $this->changedFilesDetector = $this->container->get(ChangedFilesDetector::class);
        $this->changedFilesDetector->changeConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/easy-coding-standard.yml'
        );
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->getCacheDirectory());
    }

    public function testAddFile(): void
    {
        $this->assertFileHasChanged($this->phpFile);
        $this->assertFileHasChanged($this->phpFile);
    }

    public function testHasFileChanged(): void
    {
        $this->changedFilesDetector->addFile($this->phpFile);

        $this->assertFileHasNotChanged($this->phpFile);
    }

    public function testInvalidateCacheOnConfigurationChange(): void
    {
        $this->changedFilesDetector->addFile($this->phpFile);
        $this->assertFileHasNotChanged($this->phpFile);

        $this->changedFilesDetector->changeConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/another-configuration.yml'
        );

        $this->assertFileHasChanged($this->phpFile);
    }

    private function getCacheDirectory(): string
    {
        return __DIR__ . '/cache';
    }

    private function assertFileHasChanged(string $file): void
    {
        $this->assertTrue($this->changedFilesDetector->hasFileChanged($file), sprintf(
            'Failed asserting that file "%s" has changed.',
            $file
        ));
    }

    private function assertFileHasNotChanged(string $file): void
    {
        $this->assertFalse($this->changedFilesDetector->hasFileChanged($file), sprintf(
            'Failed asserting that file "%s" has not changed.',
            $file
        ));
    }
}
