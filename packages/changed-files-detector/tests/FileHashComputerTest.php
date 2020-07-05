<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Tests;

use Nette\Utils\FileSystem;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symfony\Component\Yaml\Yaml;
use Symplify\EasyCodingStandard\ChangedFilesDetector\FileHashComputer;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class FileHashComputerTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private const INCLUDED_CONFIG_FILE = __DIR__ . '/FileHashComputerSource/another-one.yml';

    /**
     * @var FileHashComputer
     */
    private $fileHashComputer;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->fileHashComputer = self::$container->get(FileHashComputer::class);
    }

    public function testInvalidateCacheOnConfigurationChange(): void
    {
        // A. create on another one with fixer
        FileSystem::write(self::INCLUDED_CONFIG_FILE, Yaml::dump([
            'services' => [
                DeclareStrictTypesFixer::class => [],
            ],
        ]));

        $fileOneHash = $this->fileHashComputer->compute(
            __DIR__ . '/FileHashComputerSource/config-including-another-one.yml'
        );

        // B. create on another one with no fixer
        FileSystem::write(self::INCLUDED_CONFIG_FILE, Yaml::dump([
            'services' => [],
        ]));

        $fileTwoHash = $this->fileHashComputer->compute(
            __DIR__ . '/FileHashComputerSource/config-including-another-one.yml'
        );

        $this->assertNotSame($fileOneHash, $fileTwoHash);

        FileSystem::delete(self::INCLUDED_CONFIG_FILE);
    }

    public function testPhpFileHash(): void
    {
        $fileOne = __DIR__ . '/FileHashComputerSource/SomeScannedClass.php';
        $fileOneHash = $this->fileHashComputer->compute($fileOne);

        $expectedFileOneHasn = md5_file($fileOne);
        $this->assertSame($expectedFileOneHasn, $fileOneHash);

        $fileTwo = __DIR__ . '/FileHashComputerSource/ChangedScannedClass.php';
        $fileTwoHash = $this->fileHashComputer->compute($fileTwo);

        $expectedFileTwoHash = md5_file($fileTwo);
        $this->assertSame($expectedFileTwoHash, $fileTwoHash);

        $this->assertNotSame($fileOneHash, $fileTwoHash);
    }
}
