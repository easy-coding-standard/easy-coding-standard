<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\SnippetFormatter\Markdown;

use Iterator;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SnippetFormatter\Formatter\MarkdownSnippetFormatter;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use Webmozart\Assert\Assert;

final class MarkdownSnippetFormatterTest extends AbstractKernelTestCase
{
    /**
     * @var string
     */
    private const SPLIT_LINE_REGEX = "#\-\-\-\-\-\r?\n#";

    private MarkdownSnippetFormatter $markdownSnippetFormatter;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, [__DIR__ . '/config/array_fixer.php']);
        $this->markdownSnippetFormatter = $this->getService(MarkdownSnippetFormatter::class);
    }

    /**
     * @dataProvider provideData()
     */
    public function test(string $filePath): void
    {
        $fileContents = FileSystem::read($filePath);

        // before and after case - we want to see a change
        if (\str_contains($fileContents, '-----')) {
            [$inputContents, $expectedContents] = Strings::split($fileContents, self::SPLIT_LINE_REGEX);
        } else {
            // no change, part before and after are the same
            $inputContents = $fileContents;
            $expectedContents = $fileContents;
        }

        $inputFilePath = sys_get_temp_dir() . '/ecs_tests/' . md5((string) $inputContents) . '.php';
        FileSystem::write($inputFilePath, $inputContents);

        $configuration = new Configuration(true);

        $changedContent = $this->markdownSnippetFormatter->format($inputFilePath, $configuration);
        $this->assertSame($expectedContents, $changedContent);
    }

    public function provideData(): Iterator
    {
        $fixtureFilePaths = glob(__DIR__ . '/Fixture/*.md');
        Assert::isArray($fixtureFilePaths);

        foreach ($fixtureFilePaths as $fixtureFilePath) {
            yield [$fixtureFilePath];
        }
    }
}
