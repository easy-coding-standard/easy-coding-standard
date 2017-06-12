<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use SplFileInfo;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class FileProcessorTest extends AbstractContainerAwareTestCase
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
        $this->fileProcessor = $this->container->get(SniffFileProcessor::class);
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

    private function getFileLocation(): string
    {
        return __DIR__ . '/FileProcessorSource/SomeFile.php.inc';
    }
}
