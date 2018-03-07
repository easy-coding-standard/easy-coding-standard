<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Tests;

use Nette\Caching\Cache;
use Nette\Utils\FileSystem;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Cache\CacheFactory;
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
        /** @var Cache $cache */
        $cache = $this->container->get(CacheFactory::class)->create($this->getCacheDirectory());
        FileSystem::createDir($this->getCacheDirectory() . '_tests');

        $this->changedFilesDetector = $this->container->get(ChangedFilesDetector::class);
        $this->changedFilesDetector->setCache($cache);
    }

    protected function tearDown(): void
    {
        FileSystem::delete($this->getCacheDirectory() . '_tests');
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
            __DIR__ . '/ChangedFilesDetectorSource/another-configuration.neon'
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
