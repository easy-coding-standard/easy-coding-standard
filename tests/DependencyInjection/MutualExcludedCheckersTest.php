<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\EasyCodingStandard\SniffRunner\Application\SniffFileProcessor;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class MutualExcludedCheckersTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/MutualExcludedCheckersSource/config.php']
        );

        $fixerFileProcessor = $this->getService(FixerFileProcessor::class);
        $this->assertCount(2, $fixerFileProcessor->getCheckers());

        $sniffFileProcessor = $this->getService(SniffFileProcessor::class);
        $this->assertCount(0, $sniffFileProcessor->getCheckers());
    }
}
