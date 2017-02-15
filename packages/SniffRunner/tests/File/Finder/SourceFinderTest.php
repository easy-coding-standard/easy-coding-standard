<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\SniffRunner\Tests\File\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\SniffRunner\File\Finder\SourceFinder;
use Symplify\PackageBuilder\Adapter\Nette\ContainerFactory;

final class SourceFinderTest extends TestCase
{
    /**
     * @var SourceFinder
     */
    private $sourceFinder;

    protected function setUp()
    {
        $container = (new ContainerFactory())->createFromConfig(__DIR__ . '/../../../src/config/config.neon');
        $this->sourceFinder = $container->getByType(SourceFinder::class);
    }

    /**
     * @dataProvider provideFindData()
     */
    public function testFind(array $source, int $numberOfFoundFiles)
    {
        $this->assertCount(
            $numberOfFoundFiles,
            $this->sourceFinder->find($source)
        );
    }

    public function provideFindData() : array
    {
        return [
            [
                [__DIR__], 2
            ], [
                [__DIR__.'/SourceFinderSource'], 1
            ], [
                [__DIR__.'/SourceFinderSource/SomeFiles/SomeSource.php'], 1
            ], [
                [__DIR__.'/SourceFinderSource/SomeFiles/SomeSource.txt'], 0
            ]
        ];
    }
}
