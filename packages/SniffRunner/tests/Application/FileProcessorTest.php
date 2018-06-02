<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;
use Symplify\EasyCodingStandard\Application\CurrentFileProvider;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

final class FileProcessorTest extends TestCase
{
    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @var string
     */
    private $initialFileContent;

    /**
     * @var CurrentFileProvider
     */
    private $currentFileProvider;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/FileProcessorSource/easy-coding-standard.yml'
        );

        $this->sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->currentFileProvider = $container->get(CurrentFileProvider::class);
    }

    public function test(): void
    {
        $fileInfo = new SplFileInfo(
            $this->getFileLocation(),
            'FileProcessorSource',
            'FileProcessorSource/SomeFile.php.inc'
        );

        // part of Application
        $this->currentFileProvider->setFileInfo($fileInfo);

        $fixedContent = $this->sniffFileProcessor->processFile($fileInfo);
        $this->assertNotSame($this->initialFileContent, $fixedContent);
    }

    public function testGetSniffs(): void
    {
        $sniffs = $this->sniffFileProcessor->getCheckers();
        $this->assertCount(1, $sniffs);
    }

    private function getFileLocation(): string
    {
        return __DIR__ . '/FileProcessorSource/SomeFile.php.inc';
    }
}
