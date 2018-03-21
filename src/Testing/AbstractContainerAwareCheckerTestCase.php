<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Testing;

use PhpCsFixer\Fixer\FixerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\PackageBuilder\FileSystem\FileGuard;

abstract class AbstractContainerAwareCheckerTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    protected function setUp(): void
    {
        FileGuard::ensureFileExists($this->provideConfig(), get_called_class());
        $this->container = (new ContainerFactory())->createWithConfig($this->provideConfig());

        $this->fixerFileProcessor = $this->container->get(FixerFileProcessor::class);

        parent::setUp();
    }

    abstract protected function provideConfig(): string;

    /**
     * File should contain 0 errors
     */
    protected function doTestCorrectFile(string $correctFile): void
    {
        $symfonyFileInfo = new SymfonySplFileInfo($correctFile, '', '');
        $processedFileContent = $this->fixerFileProcessor->processFile($symfonyFileInfo);

        $this->assertSame(file_get_contents($correctFile), $processedFileContent);
    }

    protected function doTestWrongToFixedFile(string $wrongFile, string $fixedFile): void
    {
        $symfonyFileInfo = new SymfonySplFileInfo($wrongFile, '', '');
        $processedFileContent = $this->fixerFileProcessor->processFile($symfonyFileInfo);

        $this->assertSame(file_get_contents($fixedFile), $processedFileContent);
    }
}
