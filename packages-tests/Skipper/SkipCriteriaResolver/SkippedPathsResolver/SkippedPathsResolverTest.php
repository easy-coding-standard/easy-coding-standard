<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\Skipper\SkipCriteriaResolver\SkippedPathsResolver;

use Symplify\EasyCodingStandard\Skipper\SkipCriteriaResolver\SkippedPathsResolver;
use Symplify\EasyCodingStandard\Tests\Testing\AbstractTestCase;

final class SkippedPathsResolverTest extends AbstractTestCase
{
    private SkippedPathsResolver $skippedPathsResolver;

    protected function setUp(): void
    {
        $this->createContainerWithConfigs([__DIR__ . '/config/config.php']);
        $this->skippedPathsResolver = $this->make(SkippedPathsResolver::class);
    }

    public function test(): void
    {
        $skippedPaths = $this->skippedPathsResolver->resolve();
        $this->assertCount(2, $skippedPaths);

        $this->assertSame('*/Mask/*', $skippedPaths[1]);
    }
}
