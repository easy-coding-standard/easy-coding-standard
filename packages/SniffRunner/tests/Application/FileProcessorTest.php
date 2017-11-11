<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\EasyCodingStandard\Configuration\Configuration;
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

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/FileProcessorSource/easy-coding-standard.neon'
        );

        /** @var Configuration $configuration */
        $configuration = $container->get(Configuration::class);
        $configuration->resolveFromArray(['isFixer' => true]);

        $this->sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->initialFileContent = file_get_contents($this->getFileLocation());
    }

    protected function tearDown(): void
    {
        file_put_contents($this->getFileLocation(), $this->initialFileContent);
    }

    public function test(): void
    {
        $fileInfo = new SplFileInfo($this->getFileLocation());
        $initialFileHash = md5_file($this->getFileLocation());

        $this->sniffFileProcessor->processFile($fileInfo);
        $fixedFileHash = md5_file($this->getFileLocation());

        $this->assertNotSame($initialFileHash, $fixedFileHash);
    }

    public function testGetSniffs(): void
    {
        $sniffs = $this->sniffFileProcessor->getSniffs();
        $this->assertCount(1, $sniffs);
    }

    private function getFileLocation(): string
    {
        return __DIR__ . '/FileProcessorSource/SomeFile.php.inc';
    }
}
