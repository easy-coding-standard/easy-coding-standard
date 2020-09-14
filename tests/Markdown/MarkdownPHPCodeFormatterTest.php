<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Markdown;

use Iterator;
use Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Markdown\MarkdownPHPCodeFormatter;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * For testing approach @see https://github.com/symplify/easy-testing
 */
final class MarkdownPHPCodeFormatterTest extends AbstractKernelTestCase
{
    /**
     * @var MarkdownPHPCodeFormatter
     */
    private $markdownPHPCodeFormatter;

    protected function setUp(): void
    {
        self::bootKernelWithConfigs(EasyCodingStandardKernel::class, [__DIR__ . '/config/array_fixer.php']);
        $this->markdownPHPCodeFormatter = self::$container->get(MarkdownPHPCodeFormatter::class);

        /** @var EasyCodingStandardStyle $easyCodingStandardStyle */
        $easyCodingStandardStyle = self::$container->get(EasyCodingStandardStyle::class);
        $easyCodingStandardStyle->setVerbosity(OutputInterface::VERBOSITY_QUIET);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpectedFileInfos = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $changedContent = $this->markdownPHPCodeFormatter->format($inputAndExpectedFileInfos->getInputFileInfo(), true);
        $contents = $inputAndExpectedFileInfos->getExpectedFileInfo()->getContents();
        $this->assertSame($contents, $changedContent, $fixtureFileInfo->getRelativeFilePathFromCwd());
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.md');
    }
}
