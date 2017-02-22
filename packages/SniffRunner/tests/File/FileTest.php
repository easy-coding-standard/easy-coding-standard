<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\File;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Report\ErrorCollector;
use Symplify\EasyCodingStandard\SniffRunner\File\File;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class FileTest extends TestCase
{
    /**
     * @var File
     */
    private $file;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(__DIR__ . '/../../../../src/config/config.neon');
        $fileFactory = $container->getByType(FileFactory::class);
        $this->file = $fileFactory->create(__DIR__ . '/FileFactorySource/SomeFile.php', false);
    }

    public function testErrorDataCollector(): void
    {
        /** @var ErrorCollector $errorDataCollector */
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
