<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Tests\Testing\AbstractTestCase;

final class ExcludedCheckersTest extends AbstractTestCase
{
    public function test(): void
    {
        $this->createContainerWithConfigs([__DIR__ . '/ExcludedCheckersSource/config.php']);

        $fixerFileProcessor = $this->make(FixerFileProcessor::class);
        $this->assertCount(0, $fixerFileProcessor->getCheckers());
    }
}
