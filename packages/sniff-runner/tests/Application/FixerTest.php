<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use PHP_CodeSniffer\Fixer;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

final class FixerTest extends AbstractKernelTestCase
{
    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var File
     */
    private $file;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $fileFactory = $this->getService(FileFactory::class);

        $this->file = $fileFactory->createFromFileInfo(new SmartFileInfo(__DIR__ . '/FixerSource/SomeFile.php'));
        $this->fixer = $this->getService(Fixer::class);
    }

    public function testStartFile(): void
    {
        $this->assertSame('', $this->fixer->getContents());

        $this->file->parse();
        $this->fixer->startFile($this->file);

        $this->assertStringEqualsFile(__DIR__ . '/FixerSource/SomeFile.php', $this->fixer->getContents());
    }

    public function testTokenContent(): void
    {
        $this->file->parse();
        $this->fixer->startFile($this->file);

        $token = $this->fixer->getTokenContent(14);
        $this->assertSame('\\', $token);

        $this->fixer->replaceToken(14, '_');
        $token = $this->fixer->getTokenContent(14);
        $this->assertSame('_', $token);

        $this->assertStringNotEqualsFile(__DIR__ . '/FixerSource/SomeFile.php', $this->fixer->getContents());
    }

    public function testAddContent(): void
    {
        $this->file->parse();
        $this->fixer->startFile($this->file);
        $this->fixer->beginChangeSet();

        $this->fixer->addContentBefore(14, 'A');

        $token = $this->fixer->getTokenContent(14);
        $this->assertSame('A\\', $token);

        $this->fixer->addContent(14, 'B');
        $token = $this->fixer->getTokenContent(14);
        $this->assertSame('A\\B', $token);
    }

    public function testChangesets(): void
    {
        $this->file->parse();
        $this->fixer->startFile($this->file);
        $this->fixer->beginChangeSet();

        $tokenContent = $this->fixer->getTokenContent(14);
        $this->assertSame('\\', $tokenContent);

        $this->fixer->addContentBefore(14, 'A');
        $tokenContent = $this->fixer->getTokenContent(14);
        $this->assertSame('A\\', $tokenContent);

        // during the changeset, you are free to modify current token as you wish...
        $this->fixer->addContent(14, 'B');
        $tokenContent = $this->fixer->getTokenContent(14);
        $this->assertSame('A\\B', $tokenContent);

        // you can also rollback the changes...
        $this->fixer->rollbackChangeset();
        $tokenContent = $this->fixer->getTokenContent(14);
        $this->assertSame('\\', $tokenContent);

        $this->fixer->addContent(14, 'B');
        $this->fixer->endChangeSet();

        $tokenContent = $this->fixer->getTokenContent(14);
        $this->assertSame('\\B', $tokenContent);

        // ...that stops being the case after changeset is committed
        $this->fixer->addContent(14, 'C');
        $tokenContent = $this->fixer->getTokenContent(14);
        $this->assertSame('\\B', $tokenContent);
    }

    public function testAddNewline(): void
    {
        $this->file->parse();
        $this->fixer->startFile($this->file);
        $this->fixer->beginChangeSet();

        $token = $this->fixer->getTokenContent(14);
        $this->assertSame('\\', $token);

        $this->fixer->addNewline(14);
        $token = $this->fixer->getTokenContent(14);
        $this->assertSame('\\' . PHP_EOL, $token);

        $this->fixer->addNewlineBefore(14);
        $token = $this->fixer->getTokenContent(14);
        $this->assertSame(PHP_EOL . '\\' . PHP_EOL, $token);
    }

    public function testSubstrToken(): void
    {
        $this->file->parse();
        $this->fixer->startFile($this->file);
        $this->fixer->beginChangeSet();

        $token = $this->fixer->getTokenContent(15);
        $this->assertSame('EasyCodingStandard', $token);

        $this->fixer->substrToken(15, 0, 4);
        $token = $this->fixer->getTokenContent(15);
        $this->assertSame('Easy', $token);

        $this->fixer->substrToken(15, 3);
        $token = $this->fixer->getTokenContent(15);
        $this->assertSame('y', $token);

        $this->fixer->substrToken(17, 3, 0);
        $token = $this->fixer->getTokenContent(17);
        $this->assertSame('', $token);
    }
}
