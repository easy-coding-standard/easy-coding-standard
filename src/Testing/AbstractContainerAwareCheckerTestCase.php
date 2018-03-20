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

    private function doTest($expected, $input = null, SplFileInfo $file = null): void
    {
        $fixer = $this->createFixer();
        $this->assertTrue($fixer->isCandidate($input));

        $tokens = Tokens::fromCode($input);
        $fixResult = $fixer->fix(new SplFileInfo(__FILE__), $tokens);

        $this->assertSame($expected, $fixResult);
    }

    /**
     * File should contain 0 errors
     */
    protected function doTestCorrectFile(string $correctFile): void
    {
        $this->doTest(file_get_contents($correctFile), null, null);
    }

    protected function doTestWrongToFixedFile(string $wrongFile, string $fixedFile): void
    {
        $this->doTest(file_get_contents($fixedFile), file_get_contents($wrongFile), null);
    }
}

