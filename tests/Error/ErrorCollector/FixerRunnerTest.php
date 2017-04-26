<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Contract\ChangedFilesDetectorInterface;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\FixerRunner\Application\FileProcessor;
use Symplify\EasyCodingStandard\Tests\ContainerFactoryWithCustomConfig;

final class FixerRunnerTest extends TestCase
{
    /**
     * @var ErrorCollector
     */
    private $errorDataCollector;

    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    protected function setUp(): void
    {
        $container = (new ContainerFactoryWithCustomConfig)->createWithConfig(
            __DIR__ . '/FixerRunnerSource/easy-coding-standard.neon'
        );

        $this->errorDataCollector = $container->getByType(ErrorCollector::class);
        $this->fileProcessor = $container->getByType(FileProcessor::class);

        /** @var ChangedFilesDetectorInterface $changedFilesDetector */
        $changedFilesDetector = $container->getByType(ChangedFilesDetectorInterface::class);
        $changedFilesDetector->clearCache();
    }

    public function test(): void
    {
        $this->runFileProcessor();

        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
        $this->assertSame(1, $this->errorDataCollector->getFixableErrorCount());
        $this->assertSame(0, $this->errorDataCollector->getUnfixableErrorCount());

        $errorMessages = $this->errorDataCollector->getAllErrors();
        $this->assertCount(1, $errorMessages);

        /** @var Error $error */
        $error = array_pop($errorMessages)[0];
        $this->assertInstanceOf(Error::class, $error);

        $this->assertSame(
            'PHPUnit methods like `assertSame` should be used instead of `assertEquals`.',
            $error->getMessage()
        );
        $this->assertSame(9, $error->getLine());
    }

    private function runFileProcessor(): void
    {
        $runCommand = RunCommand::createForSourceFixerAndClearCache(
            [__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc'],
            false,
            true
        );

        $fileInfo = new SplFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc');

        $this->fileProcessor->setupWithCommand($runCommand);
        $this->fileProcessor->processFile($fileInfo);
    }
}
