<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Tests;

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Utils\FileSystem;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\Configuration\ConfigurationFileLoader;

final class ChangedFilesDetectorTest extends TestCase
{
    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    protected function setUp()
    {
        FileSystem::createDir($this->getCacheDirectory());

        $this->changedFilesDetector = $this->createChangedFilesDetectorFromConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/easy-coding-standard.neon'
        );
    }

    public function testAddFile()
    {
        $this->assertTrue($this->changedFilesDetector->hasFileChanged(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php'
        ));

        $this->assertFalse($this->changedFilesDetector->hasFileChanged(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php')
        );
    }

    public function testHasFileChanged()
    {
        $this->changedFilesDetector->addFile(__DIR__ . '/ChangedFilesDetectorSource/OneClass.php');

        $this->assertFalse($this->changedFilesDetector->hasFileChanged(
            __DIR__ . '/ChangedFilesDetectorSource/OneClass.php')
        );
    }

    public function testInvalidateCacheOnConfigurationChange()
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

    protected function tearDown()
    {
        FileSystem::delete($this->getCacheDirectory());
    }

    private function getCacheDirectory(): string
    {
        return __DIR__ . '/cache';
    }

    private function createChangedFilesDetectorFromConfigurationFile(string $configurationFile): ChangedFilesDetector
    {
        $configurationFileLoader = new ConfigurationFileLoader($configurationFile);
        return new ChangedFilesDetector($this->getCache(), $configurationFileLoader);
    }

    private function getCache(): Cache
    {
        return new Cache(new FileStorage($this->getCacheDirectory()));
    }
}
