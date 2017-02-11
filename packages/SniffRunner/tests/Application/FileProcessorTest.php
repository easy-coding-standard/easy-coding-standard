<?php declare(strict_types=1);

namespace Symplify\PHP7_CdeSniffer\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symplify\SniffRunner\Application\FileProcessor;
use Symplify\SniffRunner\DI\ContainerFactory;
use Symplify\SniffRunner\File\FileFactory;

final class FileProcessorTest extends TestCase
{
    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    protected function setUp()
    {
        $container = (new ContainerFactory())->create();
        $this->fileProcessor = $container->getByType(FileProcessor::class);
        $this->fileFactory = $container->getByType(FileFactory::class);
    }

    public function testProcessFiles()
    {
        $file = $this->fileFactory->create(__DIR__.'/FileProcessorSource/SomeFile.php', false);
        $this->fileProcessor->processFiles([$file], false);

        $file = $this->fileFactory->create(__DIR__.'/FileProcessorSource/SomeFile.php', true);
        $this->fileProcessor->processFiles([$file], true);
    }
}
