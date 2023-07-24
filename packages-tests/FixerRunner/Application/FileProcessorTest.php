<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\FixerRunner\Application;

use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Tests\Testing\AbstractTestCase;

final class FileProcessorTest extends AbstractTestCase
{
    private FixerFileProcessor $fixerFileProcessor;

    protected function setUp(): void
    {
        $this->createContainerWithConfigs([__DIR__ . '/FileProcessorSource/easy-coding-standard.php']);
        $this->fixerFileProcessor = $this->make(FixerFileProcessor::class);
    }

    public function testGetSortedCheckers(): void
    {
        $checkers = $this->fixerFileProcessor->getCheckers();

        $this->assertCount(3, $this->fixerFileProcessor->getCheckers());

        $this->assertInstanceOf(EncodingFixer::class, $checkers[0]);
        $this->assertInstanceOf(FullOpeningTagFixer::class, $checkers[1]);
        $this->assertInstanceOf(NoTrailingCommaInSinglelineArrayFixer::class, $checkers[2]);
    }
}
