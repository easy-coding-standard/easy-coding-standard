<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\ChangedFilesDetector\FileHashComputer;

use Symplify\EasyCodingStandard\Caching\FileHashComputer;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileSystem;

final class FileHashComputerTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private const INCLUDED_CONFIG_FILE = __DIR__ . '/Fixture/another-one.php';

    private FileHashComputer $fileHashComputer;

    private SmartFileSystem $smartFileSystem;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->fileHashComputer = $this->getService(FileHashComputer::class);
        $this->smartFileSystem = $this->getService(SmartFileSystem::class);
    }

    public function testInvalidateCacheOnConfigurationChange(): void
    {
        // A. create on another one with fixer
        $this->smartFileSystem->copy(__DIR__ . '/Source/first_config.php', self::INCLUDED_CONFIG_FILE, true);

        $fileOneHash = $this->fileHashComputer->computeConfig(
            __DIR__ . '/Fixture/config-including-another-one.php'
        );

        // B. create on another one with no fixer
        $this->smartFileSystem->copy(__DIR__ . '/Source/empty_config.php', self::INCLUDED_CONFIG_FILE, true);

        $fileTwoHash = $this->fileHashComputer->computeConfig(
            __DIR__ . '/Fixture/config-including-another-one.php'
        );

        $this->assertNotSame($fileOneHash, $fileTwoHash);

        $this->smartFileSystem->remove(self::INCLUDED_CONFIG_FILE);
    }

    public function testPhpFileHash(): void
    {
        $fileOne = __DIR__ . '/Source/SomeScannedClass.php';
        $fileOneHash = $this->fileHashComputer->compute($fileOne);

        $expectedFileOneHasn = md5_file($fileOne);
        $this->assertSame($expectedFileOneHasn, $fileOneHash);

        $fileTwo = __DIR__ . '/Source/ChangedScannedClass.php';
        $fileTwoHash = $this->fileHashComputer->compute($fileTwo);

        $expectedFileTwoHash = md5_file($fileTwo);
        $this->assertSame($expectedFileTwoHash, $fileTwoHash);

        $this->assertNotSame($fileOneHash, $fileTwoHash);
    }
}
