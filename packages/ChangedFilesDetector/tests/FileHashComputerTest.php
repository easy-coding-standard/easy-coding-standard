<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\ChangedFilesDetector\Tests;

use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use Symfony\Component\Yaml\Yaml;
use Symplify\EasyCodingStandard\ChangedFilesDetector\FileHashComputer;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class FileHashComputerTest extends AbstractContainerAwareTestCase
{
    /**
     * @var string
     */
    private $includedConfigFile = __DIR__ . '/FileHashComputerSource/another-one.yml';

    /**
     * @var FileHashComputer
     */
    private $fileHashComputer;

    protected function setUp(): void
    {
        $this->fileHashComputer = $this->container->get(FileHashComputer::class);
    }

    public function testInvalidateCacheOnConfigurationChange(): void
    {
        // A. create on another one with fixer
        file_put_contents($this->includedConfigFile, Yaml::dump([
            'checkers' => [DeclareStrictTypesFixer::class],
        ]));

        $fileOneHash = $this->fileHashComputer->compute(
            __DIR__ . '/FileHashComputerSource/config-including-another-one.yml'
        );

        // B. create on another one with no fixer
        file_put_contents($this->includedConfigFile, Yaml::dump([
            'checkers' => [],
        ]));

        $fileTwoHash = $this->fileHashComputer->compute(
            __DIR__ . '/FileHashComputerSource/config-including-another-one.yml'
        );

        $this->assertNotSame($fileOneHash, $fileTwoHash);

        unlink($this->includedConfigFile);
    }

    public function testPhpFileHash(): void
    {
        $fileOne = __DIR__ . '/FileHashComputerSource/SomeScannedClass.php';
        $fileOneHash = $this->fileHashComputer->compute($fileOne);
        $this->assertSame(md5_file($fileOne), $fileOneHash);

        $fileTwo = __DIR__ . '/FileHashComputerSource/ChangedScannedClass.php';
        $fileTwoHash = $this->fileHashComputer->compute($fileTwo);
        $this->assertSame(md5_file($fileTwo), $fileTwoHash);

        $this->assertNotSame($fileOneHash, $fileTwoHash);
    }
}
