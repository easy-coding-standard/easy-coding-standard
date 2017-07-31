<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Finder\FinderSanitizer;
use Symplify\EasyCodingStandard\Finder\SourceFinder;

final class SourceFinderTest extends TestCase
{
    public function test(): void
    {
        $sourceFinder = new SourceFinder(new FinderSanitizer);
        $foundFiles = $sourceFinder->find([__DIR__ . '/SourceFinderSource/Source']);
        $this->assertCount(1, $foundFiles);

        $foundFiles = $sourceFinder->find([__DIR__ . '/SourceFinderSource/Source/SomeClass.php.inc']);
        $this->assertCount(1, $foundFiles);
    }

    public function testSourceProviders(): void
    {
        $container = (new ContainerFactory)->createWithConfig(
            __DIR__ . '/SourceFinderSource/config-with-source-provider.neon'
        );

        $sourceFinder = $container->get(SourceFinder::class);
        $foundFiles = $sourceFinder->find([__DIR__ . '/SourceFinderSource/Source/tests']);

        $this->assertCount(1, $foundFiles);
    }
}
