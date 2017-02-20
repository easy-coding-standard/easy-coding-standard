<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

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
        $container = (new GeneralContainerFactory)->createFromConfig(__DIR__ . '/../../../../src/config/config.neon');
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

        $token = $this->fixer->getTokenContent(13);
        $this->assertSame('\\', $token);

        $this->fixer->replaceToken(13, '_');
        $token = $this->fixer->getTokenContent(13);
        $this->assertSame('_', $token);

        $this->assertStringNotEqualsFile(
            __DIR__ . '/FixerSource/SomeFile.php',
            $this->fixer->getContents()
        );
    }

    public function testAddContent()
    {
        $this->fixer->startFile($this->file);

        $this->fixer->addContentBefore(13, 'A');
        $token = $this->fixer->getTokenContent(13);
        $this->assertSame('A\\', $token);

        $this->fixer->addContent(13, 'B');
        $token = $this->fixer->getTokenContent(13);
        $this->assertSame('A\\B', $token);
    }

    public function testAddNewline()
    {
        $this->fixer->startFile($this->file);

        $token = $this->fixer->getTokenContent(13);
        $this->assertSame('\\', $token);

        $this->fixer->addNewline(13);
        $token = $this->fixer->getTokenContent(13);
        $this->assertSame('\\'.PHP_EOL, $token);

        $this->fixer->addNewlineBefore(13);
        $token = $this->fixer->getTokenContent(13);
        $this->assertSame(PHP_EOL.'\\'.PHP_EOL, $token);
    }

    public function testSubstrToken()
    {
        $this->fixer->startFile($this->file);

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
