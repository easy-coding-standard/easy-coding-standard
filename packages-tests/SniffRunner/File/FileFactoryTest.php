<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\SniffRunner\File;

use PHP_CodeSniffer\Files\File as PhpCodeSnifferFile;
use PHP_CodeSniffer\Fixer;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FileFactoryTest extends AbstractKernelTestCase
{
    private FileFactory $fileFactory;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);
        $this->fileFactory = $this->getService(FileFactory::class);
    }

    public function test(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/FileFactorySource/SomeFile.php');
        $file = $this->fileFactory->createFromFileInfo($fileInfo);
        $this->assertInstanceOf(File::class, $file);
        $this->assertInstanceOf(PhpCodeSnifferFile::class, $file);
        $this->assertInstanceOf(Fixer::class, $file->fixer);
    }
}
