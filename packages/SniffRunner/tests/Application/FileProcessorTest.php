<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\Application;

use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\FileSystem\SmartFileInfo;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class FileProcessorTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private $initialFileContent;

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    protected function setUp(): void
    {
        static::bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/FileProcessorSource/easy-coding-standard.yaml']
        );
        $this->makeConsoleOutputQuiet();

        $this->sniffFileProcessor = self::$container->get(SniffFileProcessor::class);
    }

    public function test(): void
    {
        $smartFileInfo = new SmartFileInfo(__DIR__ . '/FileProcessorSource/SomeFile.php.inc');

        $fixedContent = $this->sniffFileProcessor->processFile($smartFileInfo);
        $this->assertNotSame($this->initialFileContent, $fixedContent);
    }

    public function testGetSniffs(): void
    {
        $sniffs = $this->sniffFileProcessor->getCheckers();
        $this->assertCount(1, $sniffs);
    }

    private function makeConsoleOutputQuiet(): void
    {
        $easyCodingStandardStyle = self::$container->get(EasyCodingStandardStyle::class);
        $easyCodingStandardStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }
}
