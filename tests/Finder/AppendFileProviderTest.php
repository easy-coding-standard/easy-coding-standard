<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder;

use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\Tests\AbstractConfigContainerAwareTestCase;

final class AppendFileProviderTest extends AbstractConfigContainerAwareTestCase
{
    public function test(): void
    {
        $sourceFinder = $this->container->get(SourceFinder::class);
        $foundFiles = $sourceFinder->find([__DIR__ . '/SourceFinderSource/Source/tests']);

        $this->assertCount(3, $foundFiles);
    }

    protected function provideConfig(): string
    {
        return __DIR__ . '/SourceFinderSource/config-with-append-file-provider.yml';
    }
}
