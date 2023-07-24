<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use Symplify\EasyCodingStandard\Exception\Configuration\ConflictingCheckersLoadedException;
use Symplify\EasyCodingStandard\Tests\Testing\AbstractTestCase;

final class ConflictingCheckersTest extends AbstractTestCase
{
    public function test(): void
    {
        $this->expectException(ConflictingCheckersLoadedException::class);

        $this->createContainerWithConfigs([__DIR__ . '/ConflictingCheckersSource/config.php']);
    }
}
