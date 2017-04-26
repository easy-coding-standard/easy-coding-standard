<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\File;

use SplFileInfo;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class FileTest extends AbstractContainerAwareTestCase
{
    /**
     * @var File
     */
    private $file;

    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    protected function setUp(): void
    {
        $this->errorCollector = $this->container->getByType(ErrorCollector::class);

        /** @var FileFactory $fileFactory */
        $fileFactory = $this->container->getByType(FileFactory::class);
        $fileInfo = new SplFileInfo(__DIR__ . '/FileFactorySource/SomeFile.php');
        $this->file = $fileFactory->createFromFileInfo($fileInfo, false);
    }

    public function testErrorDataCollector(): void
    {
        $this->assertSame(0, $this->errorCollector->getErrorCount());

        $this->file->addError('Some Error', 0, 'code');
        $this->assertSame(1, $this->errorCollector->getErrorCount());
        $this->assertSame(0, $this->errorCollector->getFixableErrorCount());

        $this->file->addFixableError('Some Other Error', 0, 'code');
        $this->assertSame(2, $this->errorCollector->getErrorCount());
        $this->assertSame(1, $this->errorCollector->getFixableErrorCount());
    }

    /**
     * @expectedException \Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException
     */
    public function testNotImplementedGetErrorCount(): void
    {
        $this->file->getErrorCount();
    }

    /**
     * @expectedException \Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException
     */
    public function testNotImplementedGetErrors(): void
    {
        $this->file->getErrors();
    }

    /**
     * @expectedException \Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException
     */
    public function testNotImplementedProcess(): void
    {
        $this->file->process();
    }

    /**
     * @expectedException \Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException
     */
    public function testNotImplementedParse(): void
    {
        $this->file->parse();
    }
}
