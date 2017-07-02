<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Tests\Application;

use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;

final class FileProcessorTest extends TestCase
{
    /**
     * @var FixerFileProcessor
     */
    private $fileProcessor;

    protected function setUp(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/FileProcessorSource/easy-coding-standard.neon'
        );

        $this->fileProcessor = $container->get(FixerFileProcessor::class);
    }

    public function testGetFixers(): void
    {
        $this->assertCount(1, $this->fileProcessor->getFixers());
        $this->assertInstanceOf(NoTrailingCommaInSinglelineArrayFixer::class, $this->fileProcessor->getFixers()[0]);
    }
}
