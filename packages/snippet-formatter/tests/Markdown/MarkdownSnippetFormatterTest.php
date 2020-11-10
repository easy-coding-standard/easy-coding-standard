<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SnippetFormatter\Tests\Markdown;

use Iterator;
use Symplify\EasyCodingStandard\Configuration\Configuration;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter;
use Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern;
use Symplify\EasyTesting\DataProvider\StaticFixtureFinder;
use Symplify\EasyTesting\StaticFixtureSplitter;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Symplify\SmartFileSystem\SmartFileInfo;

/**
 * For testing approach @see https://github.com/symplify/easy-testing
 */
final class MarkdownSnippetFormatterTest extends AbstractKernelTestCase
{
    /**
     * @var SnippetFormatter
     */
    private $snippetFormatter;

    protected function setUp(): void
    {
        self::bootKernelWithConfigs(EasyCodingStandardKernel::class, [__DIR__ . '/config/array_fixer.php']);
        $this->snippetFormatter = self::$container->get(SnippetFormatter::class);

        // enable fixing
        /** @var Configuration $configuration */
        $configuration = self::$container->get(Configuration::class);
        $configuration->enableFixing();
    }

    /**
     * @dataProvider provideData()
     */
    public function test(SmartFileInfo $fixtureFileInfo): void
    {
        $inputAndExpectedFileInfos = StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos(
            $fixtureFileInfo
        );

        $changedContent = $this->snippetFormatter->format(
            $inputAndExpectedFileInfos->getInputFileInfo(),
            SnippetPattern::MARKDOWN_PHP_SNIPPET_REGEX,
            'markdown'
        );

        $expectedFileContent = $inputAndExpectedFileInfos->getExpectedFileContent();
        $this->assertSame($expectedFileContent, $changedContent, $fixtureFileInfo->getRelativeFilePathFromCwd());
    }

    public function provideData(): Iterator
    {
        return StaticFixtureFinder::yieldDirectory(__DIR__ . '/Fixture', '*.md');
    }
}
