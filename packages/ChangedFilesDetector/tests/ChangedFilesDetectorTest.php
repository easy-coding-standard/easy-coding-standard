<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Tests;

use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Cache\CacheFactory;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Contract\ChangedFilesDetectorInterface;

final class ChangedFilesDetectorTest extends TestCase
{
    /**
     * @var ChangedFilesDetectorInterface
     */
    private $changedFilesDetector;

    protected function setUp(): void
    {
        FileSystem::createDir($this->getCacheDirectory());

        $this->changedFilesDetector = $this->createChangedFilesDetectorFromConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/easy-coding-standard.neon'
        );
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->getCacheDirectory());
    }

    public function testAddFile(): void
    {
        $this->assertTrue($this->changedFilesDetector->hasFileChanged(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php'
        ));

        $this->assertFalse($this->changedFilesDetector->hasFileChanged(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php')
        );
    }

    public function testHasFileChanged(): void
    {
        $this->changedFilesDetector->addFile(__DIR__ . '/ChangedFilesDetectorSource/OneClass.php');

        $this->assertFalse($this->changedFilesDetector->hasFileChanged(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php')
        );
    }

    public function testInvalidateCacheOnConfigurationChange(): void
    {
        $this->changedFilesDetector->addFile(__DIR__ . '/ChangedFilesDetectorSource/OneClass.php');

        $this->assertFalse($this->changedFilesDetector->hasFileChanged(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php')
        );

        $changedFilesDetectorWithNewConfiguration = $this->createChangedFilesDetectorFromConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/another-configuration.neon'
        );

        $this->assertTrue($changedFilesDetectorWithNewConfiguration->hasFileChanged(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php')
        );

        $this->assertFalse($changedFilesDetectorWithNewConfiguration->hasFileChanged(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php')
        );
    }

    private function getCacheDirectory(): string
    {
        return __DIR__ . '/cache';
    }

    private function createChangedFilesDetectorFromConfigurationFile(string $configurationFile): ChangedFilesDetector
    {
        return new ChangedFilesDetector(new CacheFactory, $configurationFile);
    }
}
