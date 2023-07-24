<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection\ConflictingCheckers;

use Symplify\EasyCodingStandard\Exception\Configuration\ConflictingCheckersLoadedException;
use Symplify\EasyCodingStandard\FixerRunner\Application\FixerFileProcessor;
use Symplify\EasyCodingStandard\Tests\Testing\AbstractTestCase;

final class ConflictingCheckersTest extends AbstractTestCase
{
    public function test(): void
    {
        $this->expectException(ConflictingCheckersLoadedException::class);

        $this->createContainerWithConfigs([__DIR__ . '/config/config.php']);

        // invoke afterResolver() checks
        $this->make(FixerFileProcessor::class);
    }
}
