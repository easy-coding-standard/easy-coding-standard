<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\SniffRunner\ValueObject;

use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FileTest extends AbstractKernelTestCase
{
    private File $file;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $fileFactory = $this->getService(FileFactory::class);
        $fileInfo = new SmartFileInfo(__DIR__ . '/FileSource/SomeFile.php');

        $this->file = $fileFactory->createFromFileInfo($fileInfo);
        $this->file->processWithTokenListenersAndFileInfo([], $fileInfo, []);
    }
}
