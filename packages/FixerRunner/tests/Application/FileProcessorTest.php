<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Tests\Application;

use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;

final class FileProcessorTest extends TestCase
{
    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    protected function setUp(): void
    {
        $container = (new ContainerFactory())->createWithConfig(
            __DIR__ . '/FileProcessorSource/easy-coding-standard.neon'
        );

        $this->fixerFileProcessor = $container->get(FixerFileProcessor::class);
    }

    public function testGetFixers(): void
    {
        $this->assertCount(3, $this->fixerFileProcessor->getFixers());
    }

    public function testSortFixers(): void
    {
        $sortedFixers = $this->fixerFileProcessor->getFixers();

        $this->assertInstanceOf(EncodingFixer::class, $sortedFixers[0]);
        $this->assertInstanceOf(FullOpeningTagFixer::class, $sortedFixers[1]);
        $this->assertInstanceOf(NoTrailingCommaInSinglelineArrayFixer::class, $sortedFixers[2]);
    }
}
