<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\FixerRunner\Tests\Application;

use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
final class FileProcessorTest extends \Symplify\PackageBuilder\Testing\AbstractKernelTestCase
{
    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;
    protected function setUp() : void
    {
        $this->bootKernelWithConfigs(\Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel::class, [__DIR__ . '/FileProcessorSource/easy-coding-standard.php']);
        $this->fixerFileProcessor = $this->getService(\Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor::class);
    }
    public function testGetSortedCheckers() : void
    {
        $checkers = $this->fixerFileProcessor->getCheckers();
        $this->assertCount(3, $this->fixerFileProcessor->getCheckers());
        $this->assertInstanceOf(\PhpCsFixer\Fixer\Basic\EncodingFixer::class, $checkers[0]);
        $this->assertInstanceOf(\PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer::class, $checkers[1]);
        $this->assertInstanceOf(\PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer::class, $checkers[2]);
    }
}
