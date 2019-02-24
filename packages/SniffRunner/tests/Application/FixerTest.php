<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use PHP_CodeSniffer\Fixer;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

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

        $fileFactory = self::$container->get(FileFactory::class);

        $this->file = $fileFactory->createFromFileInfo(new SmartFileInfo(__DIR__ . '/FixerSource/SomeFile.php'));
        $this->fixer = self::$container->get(Fixer::class);
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

        $token = $this->fixer->getTokenContent(13);
        $this->assertSame('\\', $token);

        $this->fixer->replaceToken(13, '_');
        $token = $this->fixer->getTokenContent(13);
        $this->assertSame('_', $token);

        $this->assertStringNotEqualsFile(__DIR__ . '/FixerSource/SomeFile.php', $this->fixer->getContents());
    }

    public function testAddContent(): void
    {
        $this->file->parse();
        $this->fixer->startFile($this->file);
        $this->fixer->beginChangeSet();

        $this->fixer->addContentBefore(13, 'A');
        $token = $this->fixer->getTokenContent(13);
        $this->assertSame('A\\', $token);

        $this->fixer->addContent(13, 'B');
        $token = $this->fixer->getTokenContent(13);
        $this->assertSame('A\\B', $token);
    }

    public function testChangesets(): void
    {
        $this->file->parse();
        $this->fixer->startFile($this->file);
        $this->fixer->beginChangeSet();

        $this->assertSame('\\', $this->fixer->getTokenContent(13));

        $this->fixer->addContentBefore(13, 'A');
        $this->assertSame('A\\', $this->fixer->getTokenContent(13));

        // during the changeset, you are free to modify current token as you wish...
        $this->fixer->addContent(13, 'B');
        $this->assertSame('A\\B', $this->fixer->getTokenContent(13));

        // you can also rollback the changes...
        $this->fixer->rollbackChangeset();
        $this->assertSame('\\', $this->fixer->getTokenContent(13));

        $this->fixer->addContent(13, 'B');
        $this->fixer->endChangeSet();
        $this->assertSame('\\B', $this->fixer->getTokenContent(13));

        // ...that stops being the case after changeset is committed
        $this->fixer->addContent(13, 'C');
        $this->assertSame('\\B', $this->fixer->getTokenContent(13));
    }

    public function testAddNewline(): void
    {
        $this->file->parse();
        $this->fixer->startFile($this->file);
        $this->fixer->beginChangeSet();

        $token = $this->fixer->getTokenContent(13);
        $this->assertSame('\\', $token);

        $this->fixer->addNewline(13);
        $token = $this->fixer->getTokenContent(13);
        $this->assertSame('\\' . PHP_EOL, $token);

        $this->fixer->addNewlineBefore(13);
        $token = $this->fixer->getTokenContent(13);
        $this->assertSame(PHP_EOL . '\\' . PHP_EOL, $token);
    }

    public function testSubstrToken(): void
    {
        $this->file->parse();
        $this->fixer->startFile($this->file);
        $this->fixer->beginChangeSet();

        $token = $this->fixer->getTokenContent(14);
        $this->assertSame('EasyCodingStandard', $token);

        $this->fixer->substrToken(14, 0, 4);
        $token = $this->fixer->getTokenContent(14);
        $this->assertSame('Easy', $token);

        $this->fixer->substrToken(14, 3);
        $token = $this->fixer->getTokenContent(14);
        $this->assertSame('y', $token);

        $this->fixer->substrToken(16, 3, 0);
        $token = $this->fixer->getTokenContent(16);
        $this->assertSame('', $token);
    }
}
