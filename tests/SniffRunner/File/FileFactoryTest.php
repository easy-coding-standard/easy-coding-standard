<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\SniffRunner\File;

use PHP_CodeSniffer\Files\File as PhpCodeSnifferFile;
use PHP_CodeSniffer\Fixer;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\EasyCodingStandard\Testing\PHPUnit\AbstractTestCase;

final class FileFactoryTest extends AbstractTestCase
{
    public function test(): void
    {
        $fileFactory = $this->make(FileFactory::class);

        $file = $fileFactory->createFromFile(__DIR__ . '/FileFactorySource/SomeFile.php');

        $this->assertInstanceOf(File::class, $file);
        $this->assertInstanceOf(PhpCodeSnifferFile::class, $file);
        $this->assertInstanceOf(Fixer::class, $file->fixer);
    }
}
