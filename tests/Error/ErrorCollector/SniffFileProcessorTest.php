<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\CodingStandard\Sniffs\Naming\AbstractClassNameSniff;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;

final class SniffFileProcessorTest extends TestCase
{
    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/SniffRunnerSource/easy-coding-standard.neon'
        );
        $this->errorCollector = $container->get(ErrorCollector::class);
        $this->sniffFileProcessor = $container->get(SniffFileProcessor::class);

        /** @var ChangedFilesDetector $changedFilesDetector */
        $changedFilesDetector = $container->get(ChangedFilesDetector::class);
        $changedFilesDetector->clearCache();
    }

    public function test(): void
    {
        $this->runFileProcessor();

        $this->assertSame(1, $this->errorCollector->getErrorCount());
        $this->assertSame(1, $this->errorCollector->getFileDiffsCount());
        $this->assertSame(0, $this->errorCollector->getUnfixableErrorCount());

        $errorMessages = $this->errorCollector->getErrors();
        $this->assertStringEndsWith('NotPsr2Class.php.inc', key($errorMessages));

        /** @var Error $error */
        $error = array_pop($errorMessages)[0];
        $this->validateError($error);
    }

    private function runFileProcessor(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc');
        $this->sniffFileProcessor->processFile($fileInfo);
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
