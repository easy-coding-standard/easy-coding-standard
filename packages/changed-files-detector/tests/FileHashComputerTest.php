<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Tests;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symplify\EasyCodingStandard\ChangedFilesDetector\FileHashComputer;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\PhpConfigPrinter\YamlToPhpConverter;
use Symplify\SmartFileSystem\SmartFileSystem;

final class FileHashComputerTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private const INCLUDED_CONFIG_FILE = __DIR__ . '/FileHashComputerSource/another-one.php';

    /**
     * @var FileHashComputer
     */
    private $fileHashComputer;

    /**
     * @var SmartFileSystem
     */
    private $smartFileSystem;

    /**
     * @var YamlToPhpConverter
     */
    private $yamlToPhpConverter;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->fileHashComputer = $this->getService(FileHashComputer::class);
        $this->smartFileSystem = $this->getService(SmartFileSystem::class);
        $this->yamlToPhpConverter = $this->getService(YamlToPhpConverter::class);
    }

    public function testInvalidateCacheOnConfigurationChange(): void
    {
        // A. create on another one with fixer
        $this->dumpServicesToPhpConfigFile([
            DeclareStrictTypesFixer::class => [],
        ]);

        $fileOneHash = $this->fileHashComputer->computeConfig(
            __DIR__ . '/FileHashComputerSource/config-including-another-one.php'
        );

        // B. create on another one with no fixer
        $this->dumpServicesToPhpConfigFile([]);

        $fileTwoHash = $this->fileHashComputer->computeConfig(
            __DIR__ . '/FileHashComputerSource/config-including-another-one.php'
        );

        $this->assertNotSame($fileOneHash, $fileTwoHash);

        $this->smartFileSystem->remove(self::INCLUDED_CONFIG_FILE);
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

    /**
     * @param mixed[] $services
     */
    private function dumpServicesToPhpConfigFile(array $services): void
    {
        $yamlFileContent = $this->yamlToPhpConverter->convertYamlArray([
            'services' => $services,
        ]);

        $this->smartFileSystem->dumpFile(self::INCLUDED_CONFIG_FILE, $yamlFileContent);
    }
}
