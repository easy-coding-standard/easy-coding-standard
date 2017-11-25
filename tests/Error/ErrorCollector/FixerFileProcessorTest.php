<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;

final class FixerFileProcessorTest extends TestCase
{
    /**
     * @var ErrorCollector
     */
    private $errorCollector;

    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/FixerRunnerSource/phpunit-fixer-config.neon'
        );

        $this->errorCollector = $container->get(ErrorCollector::class);
        $this->fixerFileProcessor = $container->get(FixerFileProcessor::class);

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
        $this->assertCount(1, $errorMessages);

        /** @var Error $error */
        $error = array_pop($errorMessages)[0];
        $this->assertInstanceOf(Error::class, $error);

        $this->assertSame(
            'PHPUnit methods like `assertSame` should be used instead of `assertEquals`.',
            $error->getMessage()
        );
    }

    private function runFileProcessor(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc');

        $this->fixerFileProcessor->processFile($fileInfo);
    }
}
