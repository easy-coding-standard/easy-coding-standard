<?php

declare (strict_types=1);
namespace Symplify\SmartFileSystem\Tests\Finder\SmartFinder;

use Iterator;
use ECSPrefix20210509\PHPUnit\Framework\TestCase;
use Symplify\SmartFileSystem\FileSystemFilter;
use Symplify\SmartFileSystem\Finder\FinderSanitizer;
use Symplify\SmartFileSystem\Finder\SmartFinder;
final class SmartFinderTest extends \ECSPrefix20210509\PHPUnit\Framework\TestCase
{
    /**
     * @var SmartFinder
     */
    private $smartFinder;
    protected function setUp() : void
    {
        $this->smartFinder = new \Symplify\SmartFileSystem\Finder\SmartFinder(new \Symplify\SmartFileSystem\Finder\FinderSanitizer(), new \Symplify\SmartFileSystem\FileSystemFilter());
    }
    /**
     * @param string[] $paths
     * @dataProvider provideData()
     */
    public function test(array $paths, string $suffix, int $expectedCount) : void
    {
        $fileInfos = $this->smartFinder->find($paths, $suffix);
        $this->assertCount($expectedCount, $fileInfos);
    }
    public function provideData() : \Iterator
    {
        (yield [[__DIR__ . '/Fixture'], '*.twig', 2]);
        (yield [[__DIR__ . '/Fixture'], '*.txt', 1]);
        (yield [[__DIR__ . '/Fixture/some_file.twig'], '*.txt', 1]);
        (yield [[__DIR__ . '/Fixture/some_file.twig', __DIR__ . '/Fixture/nested_path'], '*.txt', 2]);
    }
}
