<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\SniffRunner\ValueObject;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\Tests\Testing\AbstractTestCase;

final class FileTest extends AbstractTestCase
{
    #[DoesNotPerformAssertions]
    public function test(): void
    {
        $fileFactory = $this->make(FileFactory::class);

        $file = $fileFactory->createFromFile(__DIR__ . '/FileSource/SomeFile.php');
        $file->processWithTokenListenersAndFilePath([], __DIR__ . '/FileSource/SomeFile.php', []);
    }
}
