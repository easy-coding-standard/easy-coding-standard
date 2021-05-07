<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\SnippetFormatter\Tests\HeredocNowdoc;

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
 * @requires PHP 7.3
 * For testing approach @see https://github.com/symplify/easy-testing
 */
final class Php73Test extends \Symplify\PackageBuilder\Testing\AbstractKernelTestCase
{
    /**
     * @var SnippetFormatter
     */
    private $snippetFormatter;
    protected function setUp() : void
    {
        $this->bootKernelWithConfigs(\Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel::class, [__DIR__ . '/config/array_fixer.php']);
        $this->snippetFormatter = $this->getService(\Symplify\EasyCodingStandard\SnippetFormatter\Formatter\SnippetFormatter::class);
        // enable fixing
        /** @var Configuration $configuration */
        $configuration = $this->getService(\Symplify\EasyCodingStandard\Configuration\Configuration::class);
        $configuration->enableFixing();
    }
    /**
     * @dataProvider provideData()
     */
    public function test(\Symplify\SmartFileSystem\SmartFileInfo $fixtureFileInfo) : void
    {
        $inputAndExpectedFileInfos = \Symplify\EasyTesting\StaticFixtureSplitter::splitFileInfoToLocalInputAndExpectedFileInfos($fixtureFileInfo);
        $changedContent = $this->snippetFormatter->format($inputAndExpectedFileInfos->getInputFileInfo(), \Symplify\EasyCodingStandard\SnippetFormatter\ValueObject\SnippetPattern::HERENOWDOC_SNIPPET_REGEX, 'herenowdoc');
        $expectedFileContent = $inputAndExpectedFileInfos->getExpectedFileContent();
        $this->assertSame($expectedFileContent, $changedContent, $fixtureFileInfo->getRelativeFilePathFromCwd());
    }
    /**
     * @return Iterator<mixed, SmartFileInfo[]>
     */
    public function provideData() : \Iterator
    {
        return \Symplify\EasyTesting\DataProvider\StaticFixtureFinder::yieldDirectory(__DIR__ . '/FixturePhp73', '*.php.inc');
    }
}
