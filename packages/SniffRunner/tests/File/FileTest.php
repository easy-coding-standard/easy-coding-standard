<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\File;

use Symplify\EasyCodingStandard\Application\CurrentFileProvider;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class FileTest extends AbstractKernelTestCase
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var CurrentFileProvider
     */
    private $currentFileProvider;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $this->errorAndDiffCollector = self::$container->get(ErrorAndDiffCollector::class);
        $this->currentFileProvider = self::$container->get(CurrentFileProvider::class);

        $fileFactory = self::$container->get(FileFactory::class);
        $fileInfo = new SmartFileInfo(__DIR__ . '/FileFactorySource/SomeFile.php');
        $this->file = $fileFactory->createFromFileInfo($fileInfo);
        $this->file->parse();

        // simulates Application cycle
        $this->currentFileProvider->setFileInfo($fileInfo);
    }

    public function testErrorDataCollector(): void
    {
        $this->assertSame(0, $this->errorAndDiffCollector->getErrorCount());

        $this->file->addError('Some Error', 0, 'code');
        $this->assertSame(1, $this->errorAndDiffCollector->getErrorCount());
        $this->assertSame(0, $this->errorAndDiffCollector->getFileDiffsCount());
    }

    public function testNotImplementedGetErrorCount(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->file->getErrorCount();
    }

    public function testNotImplementedGetErrors(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->file->getErrors();
    }

    public function testNotImplementedProcess(): void
    {
        $this->expectException(NotImplementedException::class);
        $this->file->process();
    }

    public function testNotImplementedParse(): void
    {
        $this->file->parse();
    }
}
