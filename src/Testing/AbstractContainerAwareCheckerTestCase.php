<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Testing;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\Tokenizer\Tokens;
use PHPUnit\Framework\TestCase;
use SplFileInfo;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\PackageBuilder\FileSystem\FileGuard;

abstract class AbstractContainerAwareCheckerTestCase extends TestCase
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    protected function setUp(): void
    {
        FileGuard::ensureFileExists($this->provideConfig(), get_called_class());
        $this->container = (new ContainerFactory())->createWithConfig($this->provideConfig());

        parent::setUp();
    }

    abstract protected function createFixer(): FixerInterface;

    abstract protected function provideConfig(): string;

    /**
     * File should contain 0 errors
     */
    protected function doTestCorrectFile(string $correctFile): void
    {
        $processedFileContent = $this->processFileWithFixerAndGetContent($correctFile, $this->createFixer());

        $this->assertSame(file_get_contents($correctFile), $processedFileContent);
    }

    protected function doTestWrongToFixedFile(string $wrongFile, string $fixedFile): void
    {
        $processedFileContent = $this->processFileWithFixerAndGetContent($wrongFile, $this->createFixer());

        $this->assertSame(file_get_contents($fixedFile), $processedFileContent);
    }

    private function processFileWithFixerAndGetContent(string $file, FixerInterface $fixer): string
    {
        $correctFileContent = file_get_contents($file);
        $tokens = Tokens::fromCode($correctFileContent);

        $fixer->fix(new SplFileInfo(__FILE__), $tokens);

        return $tokens->generateCode();
    }
}
