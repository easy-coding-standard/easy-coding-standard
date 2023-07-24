<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\EasyCodingStandard\Tests\Testing\AbstractTestCase;

final class MutualExcludedCheckersTest extends AbstractTestCase
{
    public function test(): void
    {
        $this->createContainerWithConfigs([__DIR__ . '/MutualExcludedCheckersSource/config.php']);

        $fixerFileProcessor = $this->make(FixerFileProcessor::class);
        $this->assertCount(2, $fixerFileProcessor->getCheckers());

        $sniffFileProcessor = $this->make(SniffFileProcessor::class);
        $this->assertCount(0, $sniffFileProcessor->getCheckers());
    }
}
