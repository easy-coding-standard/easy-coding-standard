<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder;

use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;
use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\Tests\AbstractContainerAwareTestCase;

final class SourceFinderTest extends AbstractContainerAwareTestCase
{
    public function test(): void
    {
        $sourceFinder = $this->container->get(SourceFinder::class);
        $foundFiles = $sourceFinder->find([__DIR__ . '/SourceFinderSource/Source']);
        $this->assertCount(1, $foundFiles);

        $foundFiles = $sourceFinder->find([__DIR__ . '/SourceFinderSource/Source/SomeClass.php.inc']);
        $this->assertCount(1, $foundFiles);
    }

    public function testSourceProviders(): void
    {
        $container = (new ContainerFactory())->createWithConfigs(
            [__DIR__ . '/SourceFinderSource/config-with-source-provider.yml']
        );

        $sourceFinder = $container->get(SourceFinder::class);
        $foundFiles = $sourceFinder->find([__DIR__ . '/SourceFinderSource/Source/tests']);

        $this->assertCount(1, $foundFiles);
    }

    public function testAppendFileAndSanitize(): void
    {
        $container = (new ContainerFactory())->createWithConfigs(
            [__DIR__ . '/SourceFinderSource/config-with-append-file-provider.yml']
        );

        $sourceFinder = $container->get(SourceFinder::class);
        $foundFiles = $sourceFinder->find([__DIR__ . '/SourceFinderSource/Source/tests']);

        $this->assertCount(3, $foundFiles);
    }
}
