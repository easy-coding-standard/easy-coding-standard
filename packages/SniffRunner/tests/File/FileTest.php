<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\File;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\EasyCodingStandard\Report\ErrorDataCollector;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class FileTest extends TestCase
{
    /**
     * @var File
     */
    private $file;

    protected function setUp()
    {
        $container = (new GeneralContainerFactory())->createFromConfig(__DIR__ . '/../../src/config/config.neon');
        $fileFactory = $container->getByType(FileFactory::class);
        $this->file = $fileFactory->create(__DIR__ . '/FileFactorySource/SomeFile.php', false);
    }

    public function testErrorDataCollector()
    {
        /** @var ErrorDataCollector $errorDataCollector */
        $errorDataCollector = Assert::getObjectAttribute(
            $this->file,
            'errorDataCollector'
        );
        $this->assertSame(0, $errorDataCollector->getErrorCount());

        $this->file->addError('Some Error', 0, 'code');
        $this->assertSame(1, $errorDataCollector->getErrorCount());
        $this->assertSame(0, $errorDataCollector->getFixableErrorCount());

        $this->file->addFixableError('Some Other Error', 0, 'code');
        $this->assertSame(2, $errorDataCollector->getErrorCount());
        $this->assertSame(1, $errorDataCollector->getFixableErrorCount());
    }

    /**
     * @expectedException \Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException
     */
    public function testNotImplementedGetErrorCount()
    {
        $this->file->getErrorCount();
    }

    /**
     * @expectedException \Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException
     */
    public function testNotImplementedGetErrors()
    {
        $this->file->getErrors();
    }

    /**
     * @expectedException \Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException
     */
    public function testNotImplementedProcess()
    {
        $this->file->process();
    }

    /**
     * @expectedException \Symplify\EasyCodingStandard\SniffRunner\Exception\File\NotImplementedException
     */
    public function testNotImplementedParse()
    {
        $this->file->parse();
    }
}
