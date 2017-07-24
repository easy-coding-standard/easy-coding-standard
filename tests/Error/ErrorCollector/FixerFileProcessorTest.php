<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\Error;
use Symplify\EasyCodingStandard\Error\ErrorCollector;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;

final class FixerFileProcessorTest extends TestCase
{
    /**
     * @var ErrorCollector
     */
    private $errorDataCollector;

    /**
     * @var FixerFileProcessor
     */
    private $fileProcessor;

    protected function setUp(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/FixerRunnerSource/phpunit-fixer-config.neon'
        );

        $this->errorDataCollector = $container->get(ErrorCollector::class);
        $this->fileProcessor = $container->get(FixerFileProcessor::class);
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
    }

    private function runFileProcessor(): void
    {
        $fileInfo = new SplFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc');

        $this->fileProcessor->processFile($fileInfo);
    }
}
