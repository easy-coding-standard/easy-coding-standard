<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\SniffRunner\ValueObject;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class FileTest extends AbstractKernelTestCase
{
    #[DoesNotPerformAssertions]
    public function test(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $fileFactory = $this->getService(FileFactory::class);

        $file = $fileFactory->createFromFile(__DIR__ . '/FileSource/SomeFile.php');
        $file->processWithTokenListenersAndFilePath([], __DIR__ . '/FileSource/SomeFile.php', []);
    }
}
