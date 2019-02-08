<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\FixerRunner\Tests\Application;

use PhpCsFixer\Fixer\ArrayNotation\NoTrailingCommaInSinglelineArrayFixer;
use PhpCsFixer\Fixer\Basic\EncodingFixer;
use PhpCsFixer\Fixer\PhpTag\FullOpeningTagFixer;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class FileProcessorTest extends AbstractKernelTestCase
{
    /**
     * @var FixerFileProcessor
     */
    private $fixerFileProcessor;

    protected function setUp(): void
    {
        static::bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/FileProcessorSource/easy-coding-standard.yml']
        );

        $easyCodingStandardStyle = self::$container->get(EasyCodingStandardStyle::class);
        $easyCodingStandardStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);

        $this->fixerFileProcessor = self::$container->get(FixerFileProcessor::class);
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
