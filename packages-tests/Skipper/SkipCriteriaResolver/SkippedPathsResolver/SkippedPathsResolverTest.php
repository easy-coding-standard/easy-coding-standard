<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Skipper\SkipCriteriaResolver\SkippedPathsResolver;

use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\Skipper\SkipCriteriaResolver\SkippedPathsResolver;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class SkippedPathsResolverTest extends AbstractKernelTestCase
{
    private SkippedPathsResolver $skippedPathsResolver;

    protected function setUp(): void
    {
        $this->bootKernelWithConfigs(EasyCodingStandardKernel::class, [__DIR__ . '/config/config.php']);
        $this->skippedPathsResolver = $this->getService(SkippedPathsResolver::class);
    }

    public function test(): void
    {
        $skippedPaths = $this->skippedPathsResolver->resolve();
        $this->assertCount(2, $skippedPaths);

        $this->assertSame('*/Mask/*', $skippedPaths[1]);
    }
}
