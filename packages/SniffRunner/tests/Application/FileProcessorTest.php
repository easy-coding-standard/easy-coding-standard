<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\Application\FileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\File\FileFactory;
use Symplify\PackageBuilder\Adapter\Nette\ContainerFactory;

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
        $container = (new ContainerFactory())->createFromConfig(__DIR__ . '/../../src/config/config.neon');
        $this->fileProcessor = $container->getByType(FileProcessor::class);
        $this->fileFactory = $container->getByType(FileFactory::class);
    }

    public function testProcessFiles()
    {
        $file = $this->fileFactory->create(__DIR__.'/FileProcessorSource/SomeFile.php', false);
        $tokensBefore = $file->getTokens();
        $this->fileProcessor->processFiles([$file], false);

        $tokensAfter = $file->getTokens();
        $this->assertSame($tokensBefore, $tokensAfter);

        $file = $this->fileFactory->create(__DIR__.'/FileProcessorSource/SomeFile.php', true);
        $tokensBefore = $file->getTokens();
        $this->fileProcessor->processFiles([$file], true);

        $tokensAfter = $file->getTokens();
        $this->assertSame($tokensBefore, $tokensAfter);
    }
}
