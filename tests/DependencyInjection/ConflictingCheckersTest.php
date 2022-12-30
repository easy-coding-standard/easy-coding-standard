<?php

declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use Symplify\EasyCodingStandard\Exception\Configuration\ConflictingCheckersLoadedException;
use Symplify\EasyCodingStandard\Kernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;

final class ConflictingCheckersTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->expectException(ConflictingCheckersLoadedException::class);

        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/ConflictingCheckersSource/config.php']
        );
    }
}
