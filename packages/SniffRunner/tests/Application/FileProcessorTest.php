<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\CodingStandard\Sniffs\Classes\FinalInterfaceSniff;
use Symplify\EasyCodingStandard\Application\Command\RunCommandFactory;
use Symplify\EasyCodingStandard\Configuration\ConfigurationOptions;
use Symplify\EasyCodingStandard\SniffRunner\Application\FileProcessor;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class FileProcessorTest extends TestCase
{
    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    /**
     * @var string
     */
    private $initialFileContent;

    /**
     * @var RunCommandFactory
     */
    private $runCommandFactory;

    protected function setUp(): void
    {
        $containerFactory = new GeneralContainerFactory;
        $container = $containerFactory->createFromConfig(__DIR__ . '/../../../../src/config/config.neon');

        $this->fileProcessor = $container->getByType(FileProcessor::class);
        $this->runCommandFactory = $container->getByType(RunCommandFactory::class);
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

        $runCommand = $this->runCommandFactory->create([__DIR__], true, true, [
            ConfigurationOptions::CHECKERS => [
                FinalInterfaceSniff::class
            ]
        ]);

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
