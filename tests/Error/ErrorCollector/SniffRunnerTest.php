<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff;
use Symplify\EasyCodingStandard\Application\Command\RunCommand;
use Symplify\EasyCodingStandard\ChangedFilesDetector\Contract\ChangedFilesDetectorInterface;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\Skipper;
use Symplify\EasyCodingStandard\SniffRunner\Application\FileProcessor;
use Symplify\PackageBuilder\Adapter\Nette\GeneralContainerFactory;

final class SniffRunnerTest extends TestCase
{
    /**
     * @var ErrorCollector
     */
    private $errorDataCollector;

    /**
     * @var Skipper
     */
    private $skipper;

    /**
     * @var FileProcessor
     */
    private $fileProcessor;

    protected function setUp(): void
    {
        $container = (new GeneralContainerFactory)->createFromConfig(
            __DIR__ . '/../../../src/config/config.neon'
        );
        $this->errorDataCollector = $container->getByType(ErrorCollector::class);
        $this->fileProcessor = $container->getByType(FileProcessor::class);
        $this->skipper = $container->getByType(Skipper::class);

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

        $errorMessages = $this->errorDataCollector->getErrors();
        $this->assertCount(1, $errorMessages);

        $this->assertStringEndsWith('NotPsr2Class.php.inc', key($errorMessages));

        /** @var Error $error */
        $error = array_pop($errorMessages)[0];
        $this->assertInstanceOf(Error::class, $error);

        $this->assertSame(5, $error->getLine());
        $this->assertSame('Abstract class should have prefix "Abstract".', $error->getMessage());
        $this->assertSame(AbstractClassNameSniff::class, $error->getSourceClass());
        $this->assertTrue($error->isFixable());
    }

    public function testSkipper(): void
    {
        $runCommand = $this->createRunCommand();

        $this->skipper->setSkipped([
            __DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc' => [AbstractClassNameSniff::class]
        ]);
        $this->fileProcessor->setupWithCommand($runCommand);

        $errorMessages = $this->errorDataCollector->getErrors();
        $this->assertCount(0, $errorMessages);
    }

    private function createRunCommand(): RunCommand
    {
        return RunCommand::createFromSourceFixerAndData(
            [__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc'],
            false,
            true,
            [
                RunCommand::PHP_CODE_SNIFFER_KEY => [AbstractClassNameSniff::class]
            ]
        );
    }
}
