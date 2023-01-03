<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\SniffRunner\ValueObject;

use SplFileInfo;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class FileTest extends AbstractKernelTestCase
{
    /**
     * @doesNotPerformAssertions
     */
    public function test(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $fileFactory = $this->getService(FileFactory::class);
        $fileInfo = new SplFileInfo(__DIR__ . '/FileSource/SomeFile.php');

        $file = $fileFactory->createFromFileInfo($fileInfo);
        $file->processWithTokenListenersAndFileInfo([], $fileInfo, []);
    }
}
