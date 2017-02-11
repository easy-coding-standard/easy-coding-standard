<?php declare(strict_types=1);

namespace Symplify\PHP7_CdeSniffer\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\Application\Fixer;
use Symplify\EasyCodingStandard\SniffRunner\DI\ContainerFactory;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;

final class FixerTest extends TestCase
{
    /**
     * @var Fixer
     */
    private $fixer;

    /**
     * @var File
     */
    private $file;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $fileFactory = $container->getByType(FileFactory::class);
        $this->file = $fileFactory->create(__DIR__ . '/FixerSource/SomeFile.php', true);
        $this->fixer = $container->getByType(Fixer::class);
    }

    public function testStartFile()
    {
        $this->assertSame('', $this->fixer->getContents());
        $this->fixer->startFile($this->file);

        $this->assertStringEqualsFile(
            __DIR__ . '/FixerSource/SomeFile.php',
            $this->fixer->getContents()
        );
    }

    public function testTokenContent()
    {
        $this->fixer->startFile($this->file);

        $token = $this->fixer->getTokenContent(5);
        $this->assertSame('\\', $token);

        $this->fixer->replaceToken(5, '_');
        $token = $this->fixer->getTokenContent(5);
        $this->assertSame('_', $token);

        $this->assertStringNotEqualsFile(
            __DIR__ . '/FixerSource/SomeFile.php',
            $this->fixer->getContents()
        );
    }

    public function testAddContent()
    {
        $this->fixer->startFile($this->file);

        $this->fixer->addContentBefore(5, 'A');
        $token = $this->fixer->getTokenContent(5);
        $this->assertSame('A\\', $token);

        $this->fixer->addContent(5, 'B');
        $token = $this->fixer->getTokenContent(5);
        $this->assertSame('A\\B', $token);
    }

    public function testAddNewline()
    {
        $this->fixer->startFile($this->file);

        $token = $this->fixer->getTokenContent(5);
        $this->assertSame('\\', $token);

        $this->fixer->addNewline(5);
        $token = $this->fixer->getTokenContent(5);
        $this->assertSame('\\'.PHP_EOL, $token);

        $this->fixer->addNewlineBefore(5);
        $token = $this->fixer->getTokenContent(5);
        $this->assertSame(PHP_EOL.'\\'.PHP_EOL, $token);
    }

    public function testSubstrToken()
    {
        $this->fixer->startFile($this->file);

        $token = $this->fixer->getTokenContent(6);
        $this->assertSame('SniffRunner', $token);

        $this->fixer->substrToken(6, 0, 4);
        $token = $this->fixer->getTokenContent(6);
        $this->assertSame('Snif', $token);

        $this->fixer->substrToken(6, 3);
        $token = $this->fixer->getTokenContent(6);
        $this->assertSame('f', $token);

        $this->fixer->substrToken(8, 3, 0);
        $token = $this->fixer->getTokenContent(8);
        $this->assertSame('', $token);
    }

    public function testLegacyEmptyMethods()
    {
        $this->fixer->startFile($this->file);
        $this->fixer->beginChangeset();
        $this->fixer->endChangeset();
    }
}
