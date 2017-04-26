<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Contract\ChangedFilesDetectorInterface;
use Symplify\EasyCodingStandard\Configuration\ConfigurationOptions;
use Symplify\EasyCodingStandard\DI\ContainerFactory;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\SniffRunner\Application\FileProcessor;

final class SniffRunnerTest extends TestCase
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
        $container = (new ContainerFactory)->create();

        $this->errorDataCollector = $container->getByType(ErrorCollector::class);
        $this->fileProcessor = $container->getByType(FileProcessor::class);

        /** @var ChangedFilesDetectorInterface $changedFilesDetector */
        $changedFilesDetector = $container->getByType(ChangedFilesDetectorInterface::class);
        $changedFilesDetector->clearCache();
    }

    public function test(): void
    {
        $runCommand = $this->createRunCommand();
        $this->fileProcessor->setupWithCommand($runCommand);
        $fileInfo = new SplFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc');
        $this->fileProcessor->processFile($fileInfo);

        $this->assertSame(1, $this->errorDataCollector->getErrorCount());
        $this->assertSame(1, $this->errorDataCollector->getFixableErrorCount());
        $this->assertSame(0, $this->errorDataCollector->getUnfixableErrorCount());

        $errorMessages = $this->errorDataCollector->getAllErrors();

        $this->assertStringEndsWith('NotPsr2Class.php.inc', key($errorMessages));

        /** @var Error $error */
        $error = array_pop($errorMessages)[0];
        $this->validateError($error);
    }

    private function createRunCommand(): RunCommand
    {
        return RunCommand::createForSourceFixerAndClearCache(
            [__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc'],
            false,
            true
        );
    }

    private function validateError(Error $error): void
    {
        $this->assertInstanceOf(Error::class, $error);

        $this->assertSame(5, $error->getLine());
        $this->assertSame('Abstract class should have prefix "Abstract".', $error->getMessage());
        $this->assertSame(AbstractClassNameSniff::class, $error->getSourceClass());
        $this->assertTrue($error->isFixable());
    }
}
