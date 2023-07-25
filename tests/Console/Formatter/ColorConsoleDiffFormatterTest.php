<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Console\Formatter;

use Iterator;
use Nette\Utils\FileSystem;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Console\Formatter\ColorConsoleDiffFormatter;

final class ColorConsoleDiffFormatterTest extends TestCase
{
    private ColorConsoleDiffFormatter $colorConsoleDiffFormatter;

    protected function setUp(): void
    {
        $this->colorConsoleDiffFormatter = new ColorConsoleDiffFormatter();
    }

    #[DataProvider('provideData')]
    public function test(string $content, string $expectedFormatedFileContent): void
    {
        $formattedContent = $this->colorConsoleDiffFormatter->format($content);

        $this->assertStringEqualsFile($expectedFormatedFileContent, $formattedContent);
    }

    public static function provideData(): Iterator
    {
        yield ['...', __DIR__ . '/Fixture/expected_dots.txt'];

        yield ["-old\n+new", __DIR__ . '/Fixture/expected_old_new.txt'];

        yield [
            FileSystem::read(__DIR__ . '/Fixture/with_full_diff_by_phpunit.diff'),
            __DIR__ . '/Fixture/expected_with_full_diff_by_phpunit.diff',
        ];
    }
}
