<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Error\ErrorCollector;

use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class FixerFileProcessorTest extends AbstractKernelTestCase
{
    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/FixerRunnerSource/phpunit-fixer-config.yml']
        );

        $this->errorAndDiffCollector = self::$container->get(ErrorAndDiffCollector::class);
        $this->fixerFileProcessor = self::$container->get(FixerFileProcessor::class);

        // silent output
        $easyCodingStandardStyle = self::$container->get(EasyCodingStandardStyle::class);
        $easyCodingStandardStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }

    public function test(): void
    {
        $this->runFileProcessor();

        $this->assertSame(0, $this->errorAndDiffCollector->getErrorCount());
        $this->assertSame(1, $this->errorAndDiffCollector->getFileDiffsCount());
    }

    private function runFileProcessor(): void
    {
        $fileInfo = new SmartFileInfo(__DIR__ . '/ErrorCollectorSource/NotPsr2Class.php.inc');

        $this->fixerFileProcessor->processFile($fileInfo);
    }
}
