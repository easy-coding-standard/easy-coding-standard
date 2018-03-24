<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Testing;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\Exception\NoCheckersLoadedException;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Error\ErrorAndDiffCollector;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\FileSystem\FileGuard;
use Symplify\PackageBuilder\Finder\SymfonyFileInfoFactory;

abstract class AbstractCheckerTestCase extends TestCase
{
    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    /**
     * @var SniffFileProcessor
     */
    private $sniffFileProcessor;

    /**
     * @var ErrorAndDiffCollector
     */
    private $errorAndDiffCollector;

    protected function setUp(): void
    {
        (new FileGuard())->ensureFileExists($this->provideConfig(), get_called_class());

        $container = (new ContainerFactory())->createWithConfig($this->provideConfig());

        $this->fixerFileProcessor = $container->get(FixerFileProcessor::class);
        $this->sniffFileProcessor = $container->get(SniffFileProcessor::class);
        $this->errorAndDiffCollector = $container->get(ErrorAndDiffCollector::class);

        parent::setUp();
    }

    abstract protected function provideConfig(): string;

    /**
     * File should stay the same and contain 0 errors
     * @todo resolve their combination with PSR-12
     */
    protected function doTestCorrectFile(string $correctFile): void
    {
        $this->ensureSomeCheckersAreRegistered();

        $symfonyFileInfo = SymfonyFileInfoFactory::createFromFilePath($correctFile);

        if ($this->fixerFileProcessor->getCheckers()) {
            $processedFileContent = $this->fixerFileProcessor->processFile($symfonyFileInfo);

            $this->assertStringEqualsFile($correctFile, $processedFileContent);
        }

        if ($this->sniffFileProcessor->getCheckers()) {
            $processedFileContent = $this->sniffFileProcessor->processFile($symfonyFileInfo);

            $this->assertSame(0, $this->errorAndDiffCollector->getErrorCount());
            $this->assertStringEqualsFile($correctFile, $processedFileContent);
        }
    }

    /**
     * @todo resolve their combination with PSR-12
     */
    protected function doTestWrongToFixedFile(string $wrongFile, string $fixedFile): void
    {
        $this->ensureSomeCheckersAreRegistered();

        $symfonyFileInfo = SymfonyFileInfoFactory::createFromFilePath($wrongFile);

        if ($this->fixerFileProcessor->getCheckers()) {
            $processedFileContent = $this->fixerFileProcessor->processFile($symfonyFileInfo);
            $this->assertStringEqualsFile($fixedFile, $processedFileContent);
        }

        if ($this->sniffFileProcessor->getCheckers()) {
            $processedFileContent = $this->sniffFileProcessor->processFile($symfonyFileInfo);
            if ($this->sniffFileProcessor->getDualRunCheckers()) {
                $processedFileContent = $this->sniffFileProcessor->processFileSecondRun($symfonyFileInfo);
            }
        }

        $this->assertStringEqualsFile($fixedFile, $processedFileContent);
    }

    /**
     * @todo resolve their combination with PSR-12
     */
    protected function doTestWrongFile(string $wrongFile): void
    {
        $this->ensureSomeCheckersAreRegistered();

        $symfonyFileInfo = SymfonyFileInfoFactory::createFromFilePath($wrongFile);

        $this->sniffFileProcessor->processFile($symfonyFileInfo);
        if ($this->sniffFileProcessor->getDualRunCheckers()) {
            $this->sniffFileProcessor->processFileSecondRun($symfonyFileInfo);
        }

        $this->assertGreaterThanOrEqual(1, $this->errorAndDiffCollector->getErrorCount());
    }

    private function ensureSomeCheckersAreRegistered(): void
    {
        $totalCheckersLoaded = count($this->sniffFileProcessor->getCheckers())
            + count($this->fixerFileProcessor->getCheckers());

        if ($totalCheckersLoaded === 0) {
            throw new NoCheckersLoadedException(
                'No checkers were found. Registers them in your config in "services:" '
                . 'section, load them via "--config <file>.yml" or "--level <level> option.'
            );
        }
    }
}
