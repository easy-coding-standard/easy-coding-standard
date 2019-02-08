<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use Symplify\EasyCodingStandard\Configuration\Exception\ConflictingCheckersLoadedException;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use Symplify\PackageBuilder\Tests\AbstractKernelTestCase;

final class ConflictingCheckersTest extends AbstractKernelTestCase
{
    public function test(): void
    {
        $this->expectException(ConflictingCheckersLoadedException::class);

        $this->bootKernelWithConfigs(
            EasyCodingStandardKernel::class,
            [__DIR__ . '/ConflictingCheckersSource/config.yml']
        );
    }
}
