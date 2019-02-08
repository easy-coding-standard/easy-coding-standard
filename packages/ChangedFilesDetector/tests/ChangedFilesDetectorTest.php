<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Tests;

use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ChangedFilesDetectorTest extends AbstractKernelTestCase
{
    /**
     * @var SmartFileInfo
     */
    private $smartFileInfo;

    /**
     * @var ChangedFilesDetector
     */
    private $changedFilesDetector;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->smartFileInfo = new SmartFileInfo(__DIR__ . '/ChangedFilesDetectorSource/OneClass.php');

        $this->changedFilesDetector = self::$container->get(ChangedFilesDetector::class);
        $this->changedFilesDetector->changeConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/easy-coding-standard.yml'
        );
    }

    public function testAddFile(): void
    {
        $this->assertFileHasChanged($this->smartFileInfo);
        $this->assertFileHasChanged($this->smartFileInfo);
    }

    public function testHasFileChanged(): void
    {
        $this->changedFilesDetector->addFileInfo($this->smartFileInfo);

        $this->assertFileHasNotChanged($this->smartFileInfo);
    }

    public function testInvalidateCacheOnConfigurationChange(): void
    {
        $this->changedFilesDetector->addFileInfo($this->smartFileInfo);
        $this->assertFileHasNotChanged($this->smartFileInfo);

        $this->changedFilesDetector->changeConfigurationFile(
            __DIR__ . '/ChangedFilesDetectorSource/another-configuration.yml'
        );

        $this->assertFileHasChanged($this->smartFileInfo);
    }

    private function assertFileHasChanged(SmartFileInfo $smartFileInfo): void
    {
        $this->assertTrue(
            $this->changedFilesDetector->hasFileInfoChanged($smartFileInfo),
            sprintf('Failed asserting that file "%s" has changed.', $smartFileInfo->getRelativeFilePath())
        );
    }

    private function assertFileHasNotChanged(SmartFileInfo $smartFileInfo): void
    {
        $this->assertFalse(
            $this->changedFilesDetector->hasFileInfoChanged($smartFileInfo),
            sprintf('Failed asserting that file "%s" has not changed.', $smartFileInfo->getRelativeFilePath())
        );
    }
}
