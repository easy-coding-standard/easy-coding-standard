<?php declare(strict_types=1);

namespace Symplify\EasyCodingStandard\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symplify\EasyCodingStandard\Configuration\Exception\ConflictingCheckersLoadedException;
use Symplify\EasyCodingStandard\DependencyInjection\ContainerFactory;

final class ConflictingCheckersTest extends TestCase
{
    public function test(): void
    {
        $this->expectException(ConflictingCheckersLoadedException::class);

        (new ContainerFactory())->createWithConfigs([__DIR__ . '/ConflictingCheckersSource/config.yml']);
    }
}
