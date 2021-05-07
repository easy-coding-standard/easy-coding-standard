<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SniffRunner\Tests\File;

use PHP_CodeSniffer\Files\File as PhpCodeSnifferFile;
use PHP_CodeSniffer\Fixer;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\SniffRunner\ValueObject\File;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;
final class FileFactoryTest extends \Symplify\PackageBuilder\Testing\AbstractKernelTestCase
{
    /**
     * @var FileFactory
     */
    private $fileFactory;
    protected function setUp() : void
    {
        $this->bootKernel(\Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel::class);
        $this->fileFactory = $this->getService(\Symplify\EasyCodingStandard\SniffRunner\File\FileFactory::class);
    }
    public function test() : void
    {
        $fileInfo = new \Symplify\SmartFileSystem\SmartFileInfo(__DIR__ . '/FileFactorySource/SomeFile.php');
        $file = $this->fileFactory->createFromFileInfo($fileInfo);
        $this->assertInstanceOf(\Symplify\EasyCodingStandard\SniffRunner\ValueObject\File::class, $file);
        $this->assertInstanceOf(\PHP_CodeSniffer\Files\File::class, $file);
        $this->assertInstanceOf(\PHP_CodeSniffer\Fixer::class, $file->fixer);
    }
}
