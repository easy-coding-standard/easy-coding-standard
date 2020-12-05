<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Finder;

use Symplify\EasyCodingStandard\Finder\SourceFinder;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class SourceFinderTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->bootKernel(EasyCodingStandardKernel::class);

        $sourceFinder = $this->getService(SourceFinder::class);
        $foundFiles = $sourceFinder->find([__DIR__ . '/SourceFinderSource/Source']);
        $this->assertCount(1, $foundFiles);

        $foundFiles = $sourceFinder->find([__DIR__ . '/SourceFinderSource/Source/SomeClass.php.inc']);
        $this->assertCount(1, $foundFiles);
    }
}
