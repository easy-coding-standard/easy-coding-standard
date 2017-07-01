<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

final class FileProcessorTest extends TestCase
{
    /**
     * @var SniffFileProcessor
     */
    private $fileProcessor;

    /**
     * @var string
     */
    private $initialFileContent;

    protected function setUp(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/FileProcessorSource/easy-coding-standard.neon'
        );

        $this->fileProcessor = $container->get(SniffFileProcessor::class);
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

        $runCommand = RunCommand::createForSourceFixerAndClearCache([__DIR__], true, true);

        $this->fileProcessor->setupWithCommand($runCommand);
        $this->fileProcessor->processFile($fileInfo); // @todo: do not allow run without configuration exception?
        $fixedFileHash = md5_file($this->getFileLocation());

        $this->assertNotSame($initialFileHash, $fixedFileHash);
    }

    public function testGetSniffs(): void
    {
        $sniffs = $this->fileProcessor->getSniffs();
        $this->assertCount(1, $sniffs);
    }

    private function getFileLocation(): string
    {
        return __DIR__ . '/FileProcessorSource/SomeFile.php.inc';
    }
}
