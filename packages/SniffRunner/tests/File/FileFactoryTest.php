<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\File;

use PHP_CodeSniffer\Files\File as BaseFile;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\Contract\File\FileInterface;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\Fixer\Fixer;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class FileFactoryTest extends TestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(__DIR__ . '/../../../../src/config/config.neon');
        $this->fileFactory = $container->getByType(FileFactory::class);
    }

    public function testCreate(): void
    {
        $file = $this->fileFactory->create(__DIR__ . '/FileFactorySource/SomeFile.php', false);
        $this->assertInstanceOf(File::class, $file);
        $this->assertInstanceOf(BaseFile::class, $file);
        $this->assertInstanceOf(FileInterface::class, $file);
        $this->assertInstanceOf(Fixer::class, $file->fixer);
    }

    /**
     * @expectedException \Symplify\EasyCodingStandard\SniffRunner\Exception\File\FileNotFoundException
     */
    public function testCreateFromNotFile(): void
    {
        $this->fileFactory->create(__DIR__, false);
    }
}
