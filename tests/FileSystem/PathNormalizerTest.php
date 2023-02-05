<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\FileSystem;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\FileSystem\PathNormalizer;

final class PathNormalizerTest extends TestCase
{
    private PathNormalizer $pathNormalizer;

    protected function setUp(): void
    {
        $this->pathNormalizer = new PathNormalizer();
    }

    #[DataProvider('provideData')]
    public function test(string $inputPath, string $expectedNormalizedPath): void
    {
        $normalizedPath = $this->pathNormalizer->normalizePath($inputPath);
        $this->assertSame($expectedNormalizedPath, $normalizedPath);
    }

    /**
     * @return Iterator<string[]>
     */
    public static function provideData(): Iterator
    {
        // based on Linux
        yield ['/any/path', '/any/path'];
        yield ['\any\path', '/any/path'];
    }
}
